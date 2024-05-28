<?php
include('config.php'); 

$action = $_POST['action'] ?? '';

function respond($data) {
    echo json_encode($data);
    exit;
}

function handleCreateProduct($product) {
    $product_name = $_POST["product_name"];
    $product_price = $_POST["product_price"];
    $product_type = $_POST["product_type"];
    $status = $product->createProduct($product_name, $product_price, $product_type);
    respond($status);
}

function handleAddVariant($product) {
    $product_id = $_POST["product_id"];
    $size = $_POST["size"];
    $color = $_POST["color"];
    $stock = $_POST["stock"];
    $status = $product->addVariant($product_id, $size, $color, $stock);
    respond($status);
}

function handleGetVariants($product) {
    $type = $_POST["type"];
    $productId = $_POST["productId"];
    if($type == 'single'){
        $list = $product->listVariant($productId);
    }else{
        $list = $product->getSubProductsByBundleId($productId);
    }
    respond($list);
}

function handlePutVariants($product) {
    $result = $product->deleteVariant($_POST["id"]);
    respond($result);
}

function handleAddStock($product) {
    $product_id = $_POST["productId"];
    $variant_id = $_POST["variant_id_select"];
    $stock_count = $_POST["stock_count"];
    $process_type = $_POST["process_type"];
    if( $process_type == "add" ){
        $status = $product->addVariantStock($product_id, $variant_id, $stock_count);
    }else{
        $status = $product->deleteVariantStock($product_id, $variant_id, $stock_count);
    }
    respond($status);
}

function handleCreateSubProduct($product) {
    $product_id = $_POST["productId_sub"];
    $variant_id = $_POST["variant_id_sub"];
    $stock_count = $_POST["stock_count_sub"];
    $status = $product->createSubProduct($product_id, $variant_id, $stock_count);
    respond($status);
}

function handleGetProducts($product) {
    $list = $product->listProductBundle();
    respond($list);
}

function handleCreateProductSub($product) {
    $product_id = $_POST['product_id_list'];
    $variant_ids = $_POST['variant_data'];
    $product->updateBundleAndStock($product_id, $variant_ids);
    respond(['status' => 'success']);
}

function handlePutSubBundle($product) {
    $result = $product->deleteSubBundle($_POST["id"]);
    respond($result);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
       // die('Geçersiz CSRF token');
    }
    switch($action) {
        case "create_product":
            handleCreateProduct($product);
            break;
        case "add_variant":
            handleAddVariant($product);
            break;
        case "get_variants":
            handleGetVariants($product);
            break;
        case "put_variants":
            handlePutVariants($product);
            break;
        case "add_stock":
            handleAddStock($product);
            break;
        case "get_products":
            handleGetProducts($product);
            break;
        case "create_product_sub":
            handleCreateProductSub($product);
            break;
        case "put_sub_bundle":
            handlePutSubBundle($product);
            break;
        case "add_sub_product":
            handleCreateSubProduct($product);
            break;
        default:
            respond(['error' => 'Invalid action']);
    }
}

?>