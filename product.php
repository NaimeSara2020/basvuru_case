<?php
    class Product {
        private $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        //Ürün Listeleme (Single)
        public function listProduct($type=''){
            $query = $this->pdo->prepare("SELECT * FROM products WHERE status=? and product_type=?");
            $query->execute([1,$type]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }

        //Ürün Listeleme (Bundle)
        public function listProductBundle(){
            $query = $this->pdo->prepare("SELECT * FROM products WHERE status=? and product_type='bundle'");
            $query->execute([1]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }

        // Ürün oluşturma
        public function createProduct($name, $price, $type) {
            $query = $this->pdo->prepare('INSERT INTO products (product_name, product_price, product_type) VALUES (?, ?, ?)');
            $query->execute([$name, $price, $type]);
            return $this->pdo->lastInsertId();
        }

        // Ürün Varyantları Listeleme
        public function listVariant($productId){
            $query = $this->pdo->prepare("SELECT * FROM variants WHERE status=? and product_id=?");
            $query->execute([1,$productId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }

        //Ürünlere Varyant Ekleme
        public function addVariant($productId, $size, $color, $stock){
            $query = $this->pdo->prepare('INSERT INTO variants (product_id, size, color, stock) VALUES (?, ?, ?, ?)');
            $query->execute([$productId, $size, $color, $stock]);
            return $this->pdo->lastInsertId();
        }

        // Ürün Varyantlarına Stok Ekleme
        public function addVariantStock($productId,$variantId,$stock){
            $query = $this->pdo->prepare('UPDATE variants SET stock = stock + ? WHERE id = ?');
            $result = $query->execute([$stock,$variantId]);
            return $result;
        }

        // Ürün Varyantlarından Stok Silme
        public function deleteVariantStock($productId,$variantId,$stock){
            $query = $this->pdo->prepare('UPDATE variants SET stock = GREATEST(stock - ?, 0) WHERE id = ? and stock > ?');
            $result = $query->execute([$stock,$variantId,0]);
            return $result;
        }

        //Ürün Varyantlarını Silme
        public function deleteVariant($id){
            $query = $this->pdo->prepare('UPDATE variants SET status = 0 WHERE id = ?');
            $result = $query->execute([$id]);
            return $result;
        }

        // Bundle Üründen Alt Ürünü Çıkartma
        public function deleteSubBundle($id){
            $query = $this->pdo->prepare('UPDATE bundle_sub_products SET status = 0 WHERE id = ?');
            $result = $query->execute([$id]);
            return $result;
        }
        

        // Alt Ürün Oluşturma
        public function createSubProduct($product_id, $variant_id, $stock_count){
            $query = $this->pdo->prepare("SELECT * FROM sub_products WHERE status = ? AND product_id = ? AND variant_id = ?");
            $query->execute([1, $product_id, $variant_id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $new_quantity = $result['quantity'] + $stock_count;
                $updateQuery = $this->pdo->prepare("UPDATE sub_products SET quantity = ? WHERE id = ?");
                $updateQuery->execute([$new_quantity, $result['id']]);
                return $result['id'];
            } else {
                $insertQuery = $this->pdo->prepare('INSERT INTO sub_products (product_id, variant_id, quantity) VALUES (?, ?, ?)');
                $insertQuery->execute([$product_id, $variant_id, $stock_count]);
                return $this->pdo->lastInsertId();
            }
        }
        
        //Bundle Ürünlere Varyant Listeleme Ve Gruplama
        public function getBundleProductOptions() {
            $query = $this->pdo->prepare("
                SELECT sp.product_id,sp.id,p.product_name, v.id as variant_id, v.size, v.color, sp.quantity
                FROM sub_products sp
                JOIN products p ON sp.product_id = p.id
                JOIN variants v ON sp.variant_id = v.id
                WHERE p.product_type = 'single'
            ");
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
            $groupedData = [];
            foreach ($results as $row) {
                $product_id = $row['product_id'];
                $variant_id = $row['variant_id'];
                $variant_stock = $row['quantity'];
                $sub_product_id = $row['id'];
                $color = $row['color'];
                $size = $row['size'];
        
                if (!isset($groupedData[$product_id])) {
                    $groupedData[$product_id] = [
                        'product_name' => $row['product_name'],
                        'colors' => []
                    ];
                }
        
                if (!isset($groupedData[$product_id]['colors'][$color])) {
                    $groupedData[$product_id]['colors'][$color] = [];
                }
        
                $groupedData[$product_id]['colors'][$color][] = [
                    'size' => $size,
                    'variant_id' => $variant_id,
                    'variant_stock' => $variant_stock,
                    'sub_id' => $sub_product_id
                ];
            }
        
            return $groupedData;
        }

        // Bundle ürün Total Stock Güncelleme
        public function updateBundleAndStock($product_id, $variant_data) {
            $bundle_id = $product_id;
            $variant_ids = array_keys($variant_data);

            foreach ($variant_data as $data){
                $query = $this->pdo->prepare("SELECT * FROM bundle_sub_products WHERE status = ? AND bundle_id = ? AND sub_product_id = ?");
                $query->execute([1, $bundle_id, $data["sub_id"]]);
                $result = $query->fetch(PDO::FETCH_ASSOC);
    
                if (!$result) {
                    $insertQuery = $this->pdo->prepare('INSERT INTO bundle_sub_products (bundle_id, sub_product_id) VALUES (?, ?)');
                    $insertQuery->execute([$bundle_id, $data["sub_id"]]);
                 }
            }
          
            $total_stock = $this->calculateBundleStock($variant_data);
            
            $query = $this->pdo->prepare("
                UPDATE products
                SET total_stock = ?
                WHERE id = ?
            ");
            $query->execute([$total_stock, $product_id]);
        }

        // Bundle  Total Stok Güncelleme Hesaplaması
        private function calculateBundleStock($variant_data) {
            $variant_ids = array_keys($variant_data);
            $variant_ids_placeholder = implode(',', array_fill(0, count($variant_ids), '?'));
           
            $query = $this->pdo->prepare("
                SELECT id, stock
                FROM variants
                WHERE id IN ($variant_ids_placeholder)
            ");
            $query->execute($variant_ids);
            $variants = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Minimum stok miktarını hesapla
            $min_stock = PHP_INT_MAX;
           
            foreach ($variants as $variant) {
                $variant_id = $variant['id'];
                $stock_count = $variant['stock'];
                $bundle_count = $variant_data[$variant_id]["quantity"]; // Alt ürün için eklenen adet
                $possible_bundle_stock = intdiv($stock_count, $bundle_count);
                if ($possible_bundle_stock < $min_stock) {
                    $min_stock = $possible_bundle_stock;
                }
            }
            return $min_stock > 0 ? $min_stock : 0;
        }

        // Bundle Alt Ürün Listelemesi İçin Ara Tablodan Veri Listelemesi
        public function getSubProductsByBundleId($bundle_id) {
            $query = $this->pdo->prepare("
            SELECT 
                p.id AS product_id,
                bs.id AS sub_id_bun,
                p.product_name AS bundle_product_name,
                p.product_price AS bundle_product_price,
                p.total_stock AS bundle_total_stock
            FROM 
                products p
                JOIN bundle_sub_products bs ON p.id = bs.bundle_id
            WHERE 
                bs.bundle_id = ?
                AND bs.status = 1
            LIMIT 1
        ");
            $query->execute([$bundle_id]);
            $bundle_info = $query->fetch(PDO::FETCH_ASSOC);
        
            if (empty($bundle_info)) {
                return [];
            }
        
            $query = $this->pdo->prepare("
                SELECT 
                    sp.product_id,
                    sp.variant_id,
                    p.product_name,
                    v.size,
                    v.color,
                    v.stock,
                    bsp.id AS bundle_sub_product_id
                FROM 
                    sub_products sp
                    JOIN products p ON sp.product_id = p.id
                    JOIN variants v ON sp.variant_id = v.id
                    JOIN bundle_sub_products bsp ON sp.id = bsp.sub_product_id
                WHERE 
                    sp.id IN (
                        SELECT sub_product_id
                        FROM bundle_sub_products 
                        WHERE bundle_id = ?
                    )
                    AND bsp.status=1
            ");
            $query->execute([$bundle_id]);
            $sub_products = $query->fetchAll(PDO::FETCH_ASSOC);
            
            $grouped_result = [];
            $color_counter = 1;
        
            foreach ($sub_products as $row) {
                
                $color = $row['color'];
                if (!isset($grouped_result[$color])) {
                    $grouped_result[$color] = [
                        'group_number' => $color_counter++,
                        'bundle_info' => $bundle_info,
                        'products' => []
                    ];
                }
                $grouped_result[$color]['products'][] = [
                    'id' => $row['variant_id'],
                    'product_id' => $row['product_id'],
                    'bundle_sub_product_id' => $row['bundle_sub_product_id'],
                    'product_name' => $row['product_name'],
                    'size' => $row['size'],
                    'stock' => $row['stock']
                ];
            }
        
            return $grouped_result;
        }
        //******************************************************************** */
    }
?>