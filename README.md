# Bundle Ürün Yönetim Sistemi

Bu proje, PHP ve MySQL kullanılarak geliştirilen bir bundle ürün yönetim sistemidir. Bundle ürünler, bir e-ticaret sitesinde birden fazla ürünün paket haline getirilerek tek bir üründe birleştirilmesini sağlar.

## Kullanılan Teknolojiler

- PHP
- MySQL

## Nasıl Çalışır?

Bu sistem, eticaret sitesi yöneticilerine ürünleri ve bu ürünlerin varyantlarını (beden, renk gibi özellikler) yönetme imkanı sunar. Ayrıca, bu ürünlerden bundle ürünler oluşturabilir ve bu bundle ürünlere alt ürünler ekleyebilir veya çıkarabilirler.

## Gereksinimler

- PHP 7.0 veya daha üstü
- MySQL veritabanı

## Kurulum

1. Projeyi klonlayın veya ZIP dosyasını indirin.
2. Veritabanı bağlantı bilgilerini `config.php` dosyasında güncelleyin.
3. `basvuru_case.sql` dosyasını kullanarak MySQL veritabanını oluşturun ve gerekli tabloları içe aktarın.

## Veritabanı Tasarımı

Bu projede, ürünler ve bundle ürünler için ayrı tablolar kullanılmıştır. Ürünlerin varyantları ve stok bilgileri ayrı tablolarda tutulmuştur.

- `products` tablosu: Ürünlerin temel bilgilerini içerir.
- `variants` tablosu: Ürün varyantlarının bilgilerini içerir.
- `sub_products` tablosu: Bundle ürünlerin alt ürünleri bilgilerini içerir.
- `bundle_sub_products` tablosu: Bundle ürünlere ait alt ürünleri bilgilerini içerir.

## Kullanılabilir Methodlar

- `listProduct($type='')`: Ürünlerin Listelemesini gösterir. Type= 'single' .
- `listProductBundle()`: Bundle Ürün Listelemesini gösterir.
- `createProduct($name, $price, $type)`: Yeni bir ürün oluşturur.
- `listVariant($productId)`: Ürün id ye göre ürün varyantlarını listeler.
- `addVariant($productId, $size, $color, $stock)`: Yeni bir varyant oluşturur.
- `addVariantStock($productId,$variantId,$stock)`: Varyant Stok ekler.
- `deleteVariantStock($productId,$variantId,$stock)`: Varyant Stok azaltır
- `deleteVariant($id)`:  Varyantları Siler.
- `deleteSubBundle($id)`: Bundle Üründen Alt Ürünleri Çıkartma
- `createSubProduct($product_id, $variant_id, $stock_count)` : Alt Ürün Oluşturma
- `getBundleProductOptions()`: Bundle Ürünlere Varyant Listeleme Ve Gruplama yapar.
- `updateBundleAndStock($product_id, $variant_data)` : Bundle ürün Total Stock Güncelleme işlemi yapar
- `calculateBundleStock($variant_data)`:  Bundle  Total Stok Güncelleme Hesaplaması yapılır.
- `getSubProductsByBundleId($bundle_id)`: Bundle Alt Ürün Listelemesi İçin Ara Tablodan Veri Listelemesi

## Veritabanı Oluşturulması

```sql
-- "basvuru_case" veritabanını oluştur
CREATE DATABASE IF NOT EXISTS basvuru_case;

-- Veritabanı kullan
USE basvuru_case;

-- Ürünler tablosu
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    product_price FLOAT NOT NULL,
    product_type ENUM('single','bundle') NOT NULL,
    total_stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    status TINYINT DEFAULT 1 NOT NULL
);

-- Bundle ürünlerin alt ürünlerini tutan tablo (Ara Tablo products-sub_products)
CREATE TABLE IF NOT EXISTS bundle_sub_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL,
    sub_product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (bundle_id) REFERENCES products(id),
    FOREIGN KEY (sub_product_id) REFERENCES sub_products(id),
    status TINYINT DEFAULT 1 NOT NULL
);

-- Alt ürünler tablosu
CREATE TABLE IF NOT EXISTS sub_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variant_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (variant_id) REFERENCES variants(id),
    status TINYINT DEFAULT 1 NOT NULL
);

-- Ürün varyantları tablosu
CREATE TABLE IF NOT EXISTS variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(50) NOT NULL,
    color VARCHAR(50) NOT NULL,
    stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    status TINYINT DEFAULT 1 NOT NULL
);