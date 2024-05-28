<?php 
    session_start();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
  include('config.php'); 
  $products = $product->listProduct('single');
  $bundle_products = $product->listProduct('bundle');
  $options = $product->getBundleProductOptions();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bundle Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
  .form-group {
    display: none;
  }
</style>
</head>
<body>
<div class="container">
    <div class="row">
        <h4>Başvuru Test Case</h4>
    <div class="col">
        <button class="btn btn-primary btn-block" onclick="toggleForm('productForm')">Ürün Oluşturma</button>
    </div>
    <div class="col">
        <button class="btn btn-primary btn-block" onclick="toggleForm('productBundleForm')">Alt Ürün Ekleme</button>
    </div>
    <div class="col">
        <button class="btn btn-primary btn-block" onclick="toggleForm('variantForm')">Varyant Oluşturma</button>
    </div>
    <div class="col">
        <button class="btn btn-primary btn-block" onclick="toggleForm('stokForm')">Stok Ekleme / Çıkarma</button>
    </div>
    <div class="col">
        <button class="btn btn-primary btn-block" onclick="toggleForm('subProductForm')">Alt Ürün Oluşturma</button>
    </div>
    </div>

    <form class="row g-3 form-group" id="productForm" method="POST" style="display: none;">
        <div><h5>Ürün Oluşturma</h5></div>
        <input type="hidden" name="action" id="action" value="create_product">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="col-auto">
            <label for="inputPassword2" class="visually">Ürün Türü</label>
            <select class="form-select" name="product_type" id="product_type" aria-label="Ürün Türü Seçiniz" required>
                <option selected>Ürün Türü</option>
                <option value="Single">Single</option>
                <option value="Bundle">Bundle</option>
            </select>
        </div>
        <div class="col-auto">
            <label for="staticEmail2" class="visually">Ürün Adı</label>
            <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Ayakkabı" required>
        </div>
        <div class="col-auto">
            <label for="inputPassword2" class="visually">Ürün Fiyatı</label>
            <input type="number" step="0.01" name="product_price" class="form-control" id="product_price" placeholder="100 ₺" required>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Gönder</button>
        </div>
    </form>

    <form  class="row g-3 form-group" id="productBundleForm" method="POST"  style="display: none;">
        <div><h5>Ürün (Bundle) Alt Ürün Ekleme</h5></div>
        <div class="col-auto">
            <label for="inputPassword2" class="visually">Ürün Adı </label>
            <select class="form-select" name="product_id_list" id="product_id_list" aria-label="Ürünü Adı Seçiniz">
            </select>
        </div>
        <div class="col-auto">
            <label for="product"  class="visually">Ürün:</label>
            <select id="product" name="product_id" onchange="updateColorsAndSizes()" class="form-select">
                <?php foreach ($options as $product_id => $product): ?>
                    <option value="<?= htmlspecialchars($product_id) ?>"><?= htmlspecialchars($product["product_name"]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label for="color"  class="visually">Renk:</label>
            <select id="color" class="form-select" name="color" onchange="updateSizes()">
                
            </select>
        </div>

        <div class="col-auto">
            <label for="size"  class="visually">Beden:</label>
            <select id="size" class="form-select" name="size" onchange="updateVariantId()">
               
            </select>
        </div>
        <input type="hidden" name="action" id="action" value="create_product_sub">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" id="variant_id" name="variant_id" value="">
        <input type="hidden" id="variant_stock" name="variant_stock" value="">
        <input type="hidden" id="sub_id" name="sub_id" value="">

        

        <button type="button" onclick="addVariant()">Alt Ürün Ekle</button>
        <div id="selected_variants"></div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Gönder</button>
        </div>
    </form>

    <form class="row g-3 form-group" id="variantForm" method="POST">
            <div><h5>Varyant Oluşturma</h5></div>
            <input type="hidden" name="action" id="action" value="add_variant">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="col-auto">
                <label for="inputPassword2" class="visually">Ürün Adı</label>
                <select class="form-select" name="product_id" id="product_id" aria-label="Ürünü Seçiniz">
                    <option selected>Ürün Seçiniz</option>
                    <?php foreach ($products as $prod) { ?>
                        <option value="<?php echo htmlspecialchars($prod["id"]) ?>"><?php echo htmlspecialchars($prod["product_name"]) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto">
                <label for="staticEmail2" class="visually">Varyant Boyut</label>
                <input type="text" name="size" class="form-control" id="size" placeholder="40">
            </div>
            <div class="col-auto">
                <label for="inputPassword2" class="visually">Varyant Renk</label>
                <input type="text" name="color" class="form-control" id="color" placeholder="Siyah">
            </div>
            <div class="col-auto">
                <label for="inputPassword2" class="visually">Varyant Adet</label>
                <input type="number" name="stock" class="form-control" id="stock" placeholder="3">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Gönder</button>
            </div>
    </form>

    <form class="row g-3 form-group" id="stokForm" method="POST">
        <div><h5>Stok Ekleme / Çıkarma</h5></div>
            <input type="hidden" name="action" id="action" value="add_stock">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="col-auto">
                <label for="inputPassword2" class="visually">Ürün Adı </label>
                <select class="form-select" name="productId" id="productId" aria-label="Ürünü Seçiniz" onchange="get_variants(this,'single')">
                    <option selected value="">Ürün Seçiniz</option>
                    <?php foreach ($products as $prod) { ?>
                        <option value="<?php echo htmlspecialchars($prod["id"]) ?>"><?php echo htmlspecialchars($prod["product_name"]) ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-auto">
                <label for="inputPassword2" class="visually">Ürün Varyant Adı </label>
                <select class="form-select" name="variant_id_select" id="variant_id_select" aria-label="Ürünü Varyantı Seçiniz">
                </select>
            </div>
            <div class="col-auto">
                <label for="staticEmail2" class="visually">Stok Adedi</label>
                <input type="number" name="stock_count" class="form-control" id="stock_count" placeholder="1">
            </div>

            <div class="col-auto">
                <label for="inputPassword2" class="visually">İşlem Türü</label>
                <select class="form-select" name="process_type" id="process_type" aria-label="İşlem Türünü Seçiniz">
                    <option value="add">Stok Ekle</option>
                    <option value="delete">Stok Sil</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Gönder</button>
            </div>
    </form>

    <form class="row g-3 form-group" id="subProductForm" method="POST">
            <div><h5>Alt Ürün Oluşturma</h5></div>
            <input type="hidden" name="action" id="action" value="add_sub_product">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="col-auto">
                <label for="inputPassword2" class="visually">Ürün Adı </label>
                <select class="form-select" name="productId_sub" id="productId_sub" aria-label="Ürünü Seçiniz" onchange="get_variants(this,'single')">
                    <option selected>Ürün Seçiniz</option>
                    <?php foreach ($products as $prod) { ?>
                        <option value="<?php echo htmlspecialchars($prod["id"]) ?>"><?php echo htmlspecialchars($prod["product_name"]) ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-auto">
                <label for="inputPassword2" class="visually">Ürün Varyant Adı </label>
                <select class="form-select" name="variant_id_sub" id="variant_id_sub" aria-label="Ürünü Varyantı Seçiniz">
                </select>
            </div>
            <div class="col-auto">
                <label for="staticEmail2" class="visually">Adet Miktarı</label>
                <input type="number" name="stock_count_sub" class="form-control" id="stock_count_sub" placeholder="1">
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Gönder</button>
            </div>
    </form>

    <div class="row" style="padding: 20px;;">
        <!-- <div class="col-12"> -->
            <div class="col-2">
            <button class="btn btn-danger btn-block" onclick="toggleTableProduct('product_table_list')">Ürün Listesi</button>
            </div>
            <div class="col-4">
                <button class="btn btn-warning btn-block" onclick="toggleTableProduct('bundle_product_table_list')">Bundle Ürün Listesi</button>
            </div>
        <!-- </div> -->
    </div>
    <table class="table visit-table-1" id="product_table_list">
        <div><h5>Ürün Listesi</h5></div>
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Ürün Adı</th>
            <th scope="col">Ürün Fiyatı</th>
            <th scope="col">Ürün Türü</th>
            <th scope="col">Ürün Varyantları</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $key => $prod) {  ?>
                <tr>
                    <th scope="row"><?php echo $key + 1; ?></th>
                    <td><?php echo htmlspecialchars($prod["product_name"]); ?></td>
                    <td><?php echo htmlspecialchars($prod["product_price"]); ?> ₺</td>
                    <td><?php echo htmlspecialchars($prod["product_type"]); ?></td>
                    <td>
                        <button type="button" onclick="getVariants(`<?php echo htmlspecialchars($prod['product_name']); ?>`,<?php echo htmlspecialchars($prod['id']); ?>,`<?php echo htmlspecialchars($prod['product_type']); ?>`)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Varyantları Listele
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table class="table visit-table-1"  id="bundle_product_table_list" style="display: none;">
        <div><h5>Bundle Ürün Listesi</h5></div>
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Ürün Adı</th>
            <th scope="col">Ürün Fiyatı</th>
            <th scope="col">Ürün Türü</th>
            <th scope="col">Ürün Varyantları</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bundle_products as $key => $prod) {  ?>
                <tr>
                    <th scope="row"><?php echo $key + 1; ?></th>
                    <td><?php echo $prod["product_name"]; ?></td>
                    <td><?php echo $prod["product_price"]; ?> ₺</td>
                    <td><?php echo $prod["product_type"]; ?></td>
                    <td>
                        <button type="button" onclick="getVariants(`<?php echo $prod['product_name']; ?>`,<?php echo $prod['id']; ?>,`<?php echo $prod['product_type']; ?>`)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Varyantları Listele
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table"  id="product-table">
                            <thead id="head_list"></thead>
                            <tbody id="productVariantTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>const options = <?= json_encode($options) ?>;</script>
<script  src="script.js?v=0.022"></script>
</body>
</html>



