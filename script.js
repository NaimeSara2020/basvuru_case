$(document).ready(function() {
    $('#productForm').on('submit', function(event) {
        event.preventDefault();
        
        if (emptyCheck($(this))) {
            $.ajax({
                type: 'POST',
                url: 'process.php',
                data: $(this).serialize(),
                success: function(response) {
                    alert('Ürün başarıyla oluşturuldu.');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Bir hata oluştu: ' + error);
                }
            });
        }
    });
    $('#variantForm').on('submit', function(event) {
        event.preventDefault();
        if (emptyCheck($(this))) {
        $.ajax({
            type: 'POST',
            url: 'process.php',
            data: $(this).serialize(),
            success: function(response) {
                alert('Ürün varyatnları başarıyla eklendi.');
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Bir hata oluştu: ' + error);
            }
        });
       }
    });          

    $('#stokForm').on('submit', function(event) {
        event.preventDefault();
        if (emptyCheck($(this))) {
        $.ajax({
            type: 'POST',
            url: 'process.php',
            data: $(this).serialize(),
            success: function(response) {
                alert('Veriler başarıyla gönderildi: ' + response);
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Bir hata oluştu: ' + error);
            }
        });
       }
    }); 
    
    $('#subProductForm').on('submit', function(event) {
        event.preventDefault();
        if (emptyCheck($(this))) {
        $.ajax({
            type: 'POST',
            url: 'process.php',
            data: $(this).serialize(),
            success: function(response) {
                alert('Veriler başarıyla gönderildi: ' + response);
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Bir hata oluştu: ' + error);
            }
        });
    }
    });  

    $('#productBundleForm').on('submit', function(event) {
        event.preventDefault();
        if (emptyCheck($(this))) {
        $.ajax({
            type: 'POST',
            url: 'process.php',
            data: $(this).serialize(),
            success: function(response) {
                alert('Veriler başarıyla gönderildi: ' + response);
                location.reload();
                // $("#productBundleForm")[0].reset()
            },
            error: function(xhr, status, error) {
                alert('Bir hata oluştu: ' + error);
            }
        });
    }
    });  
    
});
document.addEventListener('DOMContentLoaded', () => {
    updateColorsAndSizes();
    get_product()
});
function toggleForm(formId) {
    const forms = document.getElementsByClassName('form-group');
    for (let i = 0; i < forms.length; i++) {
      if (forms[i].id === formId) {
        forms[i].style.display = 'block';
      } else {
        forms[i].style.display = 'none';
      }
    }
  }

function toggleTableProduct(formId){
    const forms = document.getElementsByClassName('visit-table-1');
    for (let i = 0; i < forms.length; i++) {
      if (forms[i].id === formId) {
        forms[i].style.display = 'inline-table';
      } else {
        forms[i].style.display = 'none';
      }
    }
}

function updateColorsAndSizes() {
    const productSelect = document.getElementById('product');
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex].text;

    const colorSelect = document.getElementById('color');
    const sizeSelect = document.getElementById('size');

    colorSelect.innerHTML = '';
    sizeSelect.innerHTML = '';

    const colors = options[productId].colors;
    for (const color of Object.keys(colors)) {
        const option = document.createElement('option');
        option.value = color;
        option.textContent = color;
        colorSelect.appendChild(option);
    }

    updateSizes();
}

function updateSizes() {
    const productId = document.getElementById('product').value;
    const color = document.getElementById('color').value;
    const sizeSelect = document.getElementById('size');

    sizeSelect.innerHTML = '';

    const sizes = options[productId].colors[color];
    for (const { size } of sizes) {
        const option = document.createElement('option');
        option.value = size;
        option.textContent = size;
        sizeSelect.appendChild(option);
    }

    updateVariantId();
}

function updateVariantId() {
    const productId = document.getElementById('product').value;
    const color = document.getElementById('color').value;
    const size = document.getElementById('size').value;
    const variantIdInput = document.getElementById('variant_id');
    const variantStockInput = document.getElementById('variant_stock');
    const variantSubInput = document.getElementById('sub_id');

    const sizes = options[productId].colors[color];

    for (const { size: s, variant_id, variant_stock, sub_id } of sizes) {
        if (s === size) {
            variantIdInput.value = variant_id;
            variantStockInput.value = variant_stock;
            variantSubInput.value = sub_id;
            break;
        }
    }
}

function addVariant() {


    const productSelect = document.getElementById('product');
    const productSelectBundle = document.getElementById('product_id_list');
    const productNameBundle = productSelectBundle.options[productSelectBundle.selectedIndex].text;
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex].text;

    const color = document.getElementById('color').value;
    const size = document.getElementById('size').value;
    const variantId = document.getElementById('variant_id').value;
    const variantQuantity = document.getElementById('variant_stock').value;
    const variantSubID = document.getElementById('sub_id').value;

    if (productSelect.value === '' || productSelectBundle.value === '' || color === '' || size === '' || variantId === '' || variantQuantity === '' || variantSubID === '') {
        alert('Lütfen tüm alanları doldurun.');
        return;
    }
    
    
    const selectedVariants = document.getElementById('selected_variants');
    const div = document.createElement('div');
    div.textContent = `Bundle Ürün: ${productNameBundle} Ürün: ${productName}, Renk: ${color}, Beden: ${size}, Adet: ${variantQuantity}`;
    const inputId = document.createElement('input');
    inputId.type = 'hidden';
    inputId.name = 'variant_data[' + variantId + '][id]';
    inputId.value = variantId;
    const inputQuantity = document.createElement('input');
    inputQuantity.type = 'hidden';
    inputQuantity.name = 'variant_data[' + variantId + '][quantity]';
    inputQuantity.value = variantQuantity;
    const inputSubID = document.createElement('input');
    inputSubID.type = 'hidden';
    inputSubID.name = 'variant_data[' + variantId + '][sub_id]';
    inputSubID.value = variantSubID;
    div.appendChild(inputId);
    div.appendChild(inputSubID);
    div.appendChild(inputQuantity);
    selectedVariants.appendChild(div);
}
function getVariants(product_name,productId,type) {
    $.ajax({
        type: 'POST',
        url: 'process.php', 
        dataType: "json",
        data: {productId:productId,action:"get_variants",type:type},
        success: function(response) {
            var $tableHtml = "";
            var count = 1;
            var $tableHtmlHeader = `
                <tr>
                <th scope="col">#</th>
                <th scope="col">Renk</th>
                <th scope="col">Beden</th>
                <th scope="col">Adet Sayısı</th>
                <th scope="col">İşlemler</th>
                </tr>
                `;
            if(type == 'single'){
                response.forEach(function(item) {
                $tableHtml += "<tr>";
                $tableHtml += "<td>" + count++ + "</td>";
                $tableHtml += "<td>" + item.color + "</td>";
                $tableHtml += "<td>" + item.size + " ₺</td>";
                $tableHtml += "<td>" + item.stock + "</td>";
                $tableHtml += "<td><button type='button' onclick='putVariants(this," + item.id + ")' class='btn btn-primary'>Sil</button></td>";
                $tableHtml += "</tr>";
            });
                document.getElementById("productVariantTable").innerHTML = $tableHtml;
                document.getElementById("head_list").innerHTML = $tableHtmlHeader
            }else{
                displayProducts(response)
            }
            document.getElementById("exampleModalLabel").innerText = product_name;
        },
        error: function(xhr, status, error) {
            alert('Bir hata oluştu: ' + error);
        }
    });
}

function displayProducts(data) {
    const tableBody = document.getElementById('product-table').querySelector('tbody');
    if (!tableBody) {
        return;
    }

    let html = '';
    var $tableHtmlHeader = `
                    <tr>
                    <th scope="col">Ürün Adı</th>
                    <th scope="col">Ürün Fiyatı</th>
                    <th scope="col">Alt Ürünler</th>
                    <th scope="col">Toplam Stok</th>
                    <th scope="col">İşlemler</th>
                    </tr>
                    `;

    for (const color in data) {
        const group = data[color];
        
        group.products.forEach(product => {
            html += `
                <tr>
                    <td>${group.bundle_info.bundle_product_name}</td>
                    <td>${group.bundle_info.bundle_product_price}</td>
                    <td>${product.product_name} - ${color} - ${product.size} - ${product.stock} Adet</td>
                    <td>${group.bundle_info.bundle_total_stock}</td>
                    <td><button onclick="deleteRow(this, ${product.bundle_sub_product_id})">Sil</button></td>
                </tr>
            `;
        });
    }
    document.getElementById("head_list").innerHTML = $tableHtmlHeader
    tableBody.innerHTML = html;
}

function deleteRow(button, bundleSubProductId) {
    deleteSubProduct(bundleSubProductId, function(response) {
        const row = button.parentElement.parentElement;
        row.remove();
    });
}

function deleteSubProduct(id,callback){
    if (confirm("Silmek istediğinize emin misiniz?")) {
        $.ajax({
            type: 'POST',
            url: 'process.php',
            dataType: "json",
            data: {id:id,action:"put_sub_bundle"},
            success: function(response) {
                callback(response);
            },
            error: function(xhr, status, error) {
                alert('Bir hata oluştu: ' + error);
            }
        });
    } else {
        alert("Silme işlemi iptal edildi.");
    }
}

function get_product(){
    
    $.ajax({
        type: 'POST',
        url: 'process.php',
        dataType: "json",
        data: {action:"get_products"},
        success: function(response) {
            var $tableHtml = '<option value="" selected>Ürün Adı Seçiniz</option>';
            response.forEach(function(item) {
                $tableHtml += "<option value='" + item.id + "'>" + item.product_name + "</option>";
            });
            document.getElementById("product_id_list").innerHTML = $tableHtml;
        },
        error: function(xhr, status, error) {
            alert('Bir hata oluştu: ' + error);
        }
    });
}

function get_variants(variantId,type) {
    var variant_id = variantId.value;

    $.ajax({
        type: 'POST',
        url: 'process.php',
        dataType: "json",
        data: {productId:variant_id,action:"get_variants",type:type},
        success: function(response) {
            var groupedData = {};
            response.forEach(function(item) {
                if (!groupedData[item.color]) {
                    groupedData[item.color] = [];
                }
                groupedData[item.color].push(item);
            });
            var $tableHtml = '<option selected>Ürün Varyantı Seçiniz</option>';
            
            for (var color in groupedData) {
                $tableHtml += '<optgroup label="' + color + '">';
                groupedData[color].forEach(function(variant) {
                    $tableHtml += "<option value='" + variant.id + "'>" + variant.size + " / " + color + "</option>";
                });
                $tableHtml += '</optgroup>';
            }
            document.getElementById("variant_id_select").innerHTML = $tableHtml;
            document.getElementById("variant_id_sub").innerHTML = $tableHtml;
            
        },
        error: function(xhr, status, error) {
            alert('Bir hata oluştu: ' + error);
        }
    });
}

function putVariants(button,id) {
    if (confirm("Silmek istediğinize emin misiniz?")) {
        $.ajax({
            type: 'POST',
            url: 'process.php',
            dataType: "json",
            data: {id:id,action:"put_variants"},
            success: function(response) {
                alert('Varyant silindi: ');
                if(response){
                    const row = button.parentElement.parentElement;
                    row.remove();
                }
            },
            error: function(xhr, status, error) {
                alert('Bir hata oluştu: ' + error);
            }
        });
    } else {
        alert("Silme işlemi iptal edildi.");
    }
}

function emptyCheck(form) {
    let isValid = true;
    form.find('input').each(function() {
        if ($(this).val() === '') {
            isValid = false;
            alert('Lütfen tüm alanları doldurun.');
            return false;
        }
    });

    if (form.attr('id') === 'productBundleForm') {
        if (!form.find('input[name^="variant_data"]').length) {
            isValid = false;
            alert('Lütfen ürününüzü seçiniz ve "Alt Ürün Ekle" butonuna basınız.');
        }
    }

    return isValid;
}