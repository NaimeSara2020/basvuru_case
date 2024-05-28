<?php

class Product {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    private function executeQuery($sql, $params = []) {
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return $query;
    }

    public function listProduct($type='') {
        $sql = "SELECT * FROM products WHERE status=? AND product_type=?";
        $query = $this->executeQuery($sql, [1, $type]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listProductBundle() {
        $sql = "SELECT * FROM products WHERE status=? AND product_type='bundle'";
        $query = $this->executeQuery($sql, [1]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProduct($name, $price, $type) {
        $sql = "INSERT INTO products (product_name, product_price, product_type) VALUES (?, ?, ?)";
        $this->executeQuery($sql, [$name, $price, $type]);
        return $this->pdo->lastInsertId();
    }

    public function listVariant($productId) {
        $sql = "SELECT * FROM variants WHERE status=? AND product_id=?";
        $query = $this->executeQuery($sql, [1, $productId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addVariant($productId, $size, $color, $stock) {
        $sql = "INSERT INTO variants (product_id, size, color, stock) VALUES (?, ?, ?, ?)";
        $this->executeQuery($sql, [$productId, $size, $color, $stock]);
        return $this->pdo->lastInsertId();
    }

    public function addVariantStock($productId, $variantId, $stock) {
        $sql = "UPDATE variants SET stock = stock + ? WHERE id = ?";
        $this->executeQuery($sql, [$stock, $variantId]);
        return true;
    }

    public function deleteVariantStock($productId, $variantId, $stock) {
        $sql = "UPDATE variants SET stock = GREATEST(stock - ?, 0) WHERE id = ? AND stock > ?";
        $this->executeQuery($sql, [$stock, $variantId, 0]);
        return true;
    }

    public function deleteVariant($id) {
        $sql = "UPDATE variants SET status = 0 WHERE id = ?";
        $this->executeQuery($sql, [$id]);
        return true;
    }

    public function deleteSubBundle($id) {
        $sql = "UPDATE bundle_sub_products SET status = 0 WHERE id = ?";
        $this->executeQuery($sql, [$id]);
        return true;
    }

    public function createSubProduct($product_id, $variant_id, $stock_count) {
        $sql = "SELECT * FROM sub_products WHERE status = ? AND product_id = ? AND variant_id = ?";
        $query = $this->executeQuery($sql, [1, $product_id, $variant_id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $new_quantity = $result['quantity'] + $stock_count;
            $sql = "UPDATE sub_products SET quantity = ? WHERE id = ?";
            $this->executeQuery($sql, [$new_quantity, $result['id']]);
            return $result['id'];
        } else {
            $sql = "INSERT INTO sub_products (product_id, variant_id, quantity) VALUES (?, ?, ?)";
            $this->executeQuery($sql, [$product_id, $variant_id, $stock_count]);
            return $this->pdo->lastInsertId();
        }
    }

    public function getBundleProductOptions() {
        $sql = "
            SELECT sp.product_id,sp.id,p.product_name, v.id as variant_id, v.size, v.color, sp.quantity
            FROM sub_products sp
            JOIN products p ON sp.product_id = p.id
            JOIN variants v ON sp.variant_id = v.id
            WHERE p.product_type = 'single'
        ";
        $query = $this->executeQuery($sql);
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

    public function updateBundleAndStock($product_id, $variant_data) {
        $bundle_id = $product_id;
        $variant_ids = array_keys($variant_data);

        foreach ($variant_data as $data){
            $sql = "SELECT * FROM bundle_sub_products WHERE status = ? AND bundle_id = ? AND sub_product_id = ?";
            $query = $this->executeQuery($sql, [1, $bundle_id, $data["sub_id"]]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $sql = "INSERT INTO bundle_sub_products (bundle_id, sub_product_id) VALUES (?, ?)";
                $this->executeQuery($sql, [$bundle_id, $data["sub_id"]]);
             }
        }

        $total_stock = $this->calculateBundleStock($variant_data);

        $sql = "UPDATE products SET total_stock = ? WHERE id = ?";
        $this->executeQuery($sql, [$total_stock, $product_id]);
    }

    private function calculateBundleStock($variant_data) {
        $variant_ids = array_keys($variant_data);
        $variant_ids_placeholder = implode(',', array_fill(0, count($variant_ids), '?'));

        $sql = "SELECT id, stock FROM variants WHERE id IN ($variant_ids_placeholder)";
        $query = $this->executeQuery($sql, $variant_ids);
        $variants = $query->fetchAll(PDO::FETCH_ASSOC);

        $min_stock = PHP_INT_MAX;

        foreach ($variants as $variant) {
            $variant_id = $variant['id'];
            $stock_count = $variant['stock'];
            $bundle_count = $variant_data[$variant_id]["quantity"];
            $possible_bundle_stock = intdiv($stock_count, $bundle_count);
            if ($possible_bundle_stock < $min_stock) {
                $min_stock = $possible_bundle_stock;
            }
        }
        return $min_stock > 0 ? $min_stock : 0;
    }

    public function getSubProductsByBundleId($bundle_id) {
        $sql = "
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
        ";
        $query = $this->executeQuery($sql, [$bundle_id]);
        $bundle_info = $query->fetch(PDO::FETCH_ASSOC);

        if (empty($bundle_info)) {
            return [];
        }

        $sql = "
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
        ";
        $query = $this->executeQuery($sql, [$bundle_id]);
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
}