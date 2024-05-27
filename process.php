<?php
   
   include('config.php'); 

   $action = isset($_POST['action']) ? $_POST['action'] : '';


   if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($action == "create_product"){
            $product_name = $_POST["product_name"];
            $product_price = $_POST["product_price"];
            $product_type = $_POST["product_type"];
            $status = $product->createProduct($product_name, $product_price, $product_type);
            echo $status;
        }else if($action == "add_variant"){
            $product_id = $_POST["product_id"];
            $size = $_POST["size"];
            $color = $_POST["color"];
            $stock = $_POST["stock"];
            $status = $product->addVariant($product_id, $size, $color, $stock);
        }else if($action == "get_variants"){
            if($_POST["type"] == 'single'){
                $list = $product->listVariant($_POST["productId"]);
            }else{
                $list = $product->getSubProductsByBundleId($_POST["productId"]);
            }
             echo json_encode($list);
        }else if($action == "put_variants"){
            $result = $product->deleteVariant($_POST["id"]);
            echo $result;
        }else if($action == "add_stock"){
            $product_id = $_POST["productId"];
            $variant_id = $_POST["variant_id_select"];
            $stock_count = $_POST["stock_count"];
            $process_type = $_POST["process_type"];
            if( $process_type == "add" ){
                $status = $product->addVariantStock($product_id, $variant_id, $stock_count);
            }else{
                $status = $product->deleteVariantStock($product_id, $variant_id, $stock_count);
            }
            echo $status;
        }else if($action == "get_products"){
            $list = $product->listProductBundle();
            echo json_encode($list);
        }else if($action == "create_product_sub"){
            $product_id = $_POST['product_id_list'];
            $variant_ids = $_POST['variant_data'];
            $product->updateBundleAndStock($product_id, $variant_ids);
        }else if($action == "put_sub_bundle"){
            $result = $product->deleteSubBundle($_POST["id"]);
            echo $result;
        }
   }


?>