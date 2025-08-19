'use strict';

import * as bootstrap from 'bootstrap';

const baseUrl = window.location.origin;

window.readURL = function (input) {
    if (input.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("category-img-tag").setAttribute('src', e.target.result);
        };
        document.getElementById("imagePreview").classList.remove("d-none");
        reader.readAsDataURL(input.target.files[0]);
    }
};

document.getElementById("productImage").addEventListener(`change`, function (event) {
    readURL(event);
});

window.closeProductModal = function () {
    bootstrap.Modal.getOrCreateInstance('#productModal').hide();
    for (let i = 0; i < document.querySelectorAll('.btn_close_modal').length; i++) {
        document.querySelectorAll('.btn_close_modal')[i].addEventListener('click', function (event) {
            document.getElementById('image_preview').classList.add("d-none");
            document.getElementById('category-img-tag').setAttribute('src', '');
        });
    }
};


window.getProduct = async function (id) {
    if (!id) {
        window.modalMessage({
            title: 'Erro ao buscar produto',
            description: 'Id do produto não informado',
            type: 'error',
        });
    }

    let product = await fetch(`products/${id}`);
    let response = await product.json();

    if (response?.status === 200 && response?.data) {
        return response.data;
    } else {
        window.modalMessage({
            title: 'Erro ao buscar produto',
            description: response?.message ?? 'Produto não encontrado',
            type: 'error',
        });
    }
    return false;
};

window.openProductModal = function (product = null) {
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const title = document.getElementById('productModalTitle');

    if (product) {
        document.getElementById('updateProduct').classList.remove('d-none');
        document.getElementById('saveProduct').classList.add('d-none');
        title.textContent = 'Edit Product';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productValue').value = product.price;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productUnit').value = product.unit_measure;
        document.getElementById('productAvailable').checked = product.available;
        document.getElementById('category-img-tag').setAttribute('src', `${baseUrl}/storage/delivery/${product.image_name}`);
        document.getElementById('imagePreview').classList.remove('d-none');
    } else {
        document.getElementById('category-img-tag').setAttribute('src', '');
        document.getElementById('updateProduct').classList.add('d-none');
        document.getElementById('saveProduct').classList.remove('d-none');
        document.getElementById('imagePreview').classList.add('d-none');
        title.textContent = 'Add New Product';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
    }

    modal.show();
};

window.saveProduct = async function (action = 'create') {
    const name = document.getElementById('productName').value;
    const price = parseFloat(document.getElementById('productValue').value);
    const stock = parseInt(document.getElementById('productStock').value);
    const unitMeasure = document.getElementById('productUnit').value;
    const image = document.getElementById('productImage');
    const available = document.getElementById('productAvailable').checked;

    const data = new FormData();
    data.append('name', name);
    data.append('price', price);
    data.append('stock', stock);
    data.append('image', image.files[0]);
    data.append('available', available);
    data.append('unit_measure', unitMeasure);

    let headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    let route = 'products/new_product';
    if (action !== 'create') {
        route = `products/edit/${document.getElementById('productId').value}`;
    }

    let options = {
        method: 'POST',
        headers: headers,
        body: data
    };

    showLoading(true);
    let response = await fetch(`${route}`, options);
    let retorno = await response.json();

    if (retorno) {
        if (retorno?.status === 200 && retorno?.data) {
            closeProductModal();
            window.modalMessage({
                title: retorno.message,
                description: retorno.message,
                type: 'success',
            });
            showNewProduct(retorno.data);
        } else {
            let alerts = retorno.errors;
            let message = '';
            for (let key in alerts) {
                if (alerts.hasOwnProperty(key)) {
                    message += `${key}: ${alerts[key].join(', ')}<br>`;
                }
            }
            window.modalMessage({
                title: 'Erro ao criar produto',
                description: message,
                type: 'error',
            });
        }
    } else {
        window.modalMessage({
            title: 'Erro ao criar produto',
            description: 'Ocorreu um erro ao criar o produto',
            type: 'error',
        });
    }
    showLoading(false);

    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
};

window.showNewProduct = function (productData) {
    let newProduct = `
    <div class="col-md-6 col-lg-4 mb-4" id="product_${productData.id}">
                <div class="card h-100">
                    <img src="${baseUrl}/storage/delivery/${productData.image_name}" class="card-img-top"
                        style="height: 200px; object-fit: cover;" alt="${productData.name}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${productData.name}</h5>
                        <p class="card-text">
                            <strong>Preço:</strong> R$ ${productData.price}<br>
                            <strong>Estoque:</strong> ${productData.stock} ${productData.unit_measure}<br>
                            <strong>Status:</strong>
                            <span class="badge ${ productData.available ? 'bg-success' : 'bg-danger'}">
                                ${productData.available ? 'Disponível' : 'Indisponível' }
                            </span>
                        </p>
                        <div class="mt-auto">
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-outline-primary btn-sm" onclick="editProduct(${productData.id})
                                id="editProduct_${productData.id}">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(${productData.id})"
                                 id="deleteProduct_${productData.id}">>
                                    <i class="bi bi-trash me-1"></i>Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    `;

    document.getElementById(`productList`).innerHTML += newProduct;
};

window.editProduct = async function (id) {
    const product = await getProduct(id);

    if (!product) {
        window.modalMessage({
            title: 'Error',
            description: 'Product not found',
            type: 'error',
        });
        return;
    }
    openProductModal(product);
};

window.deleteProduct = function (id) {
    if (!id) {
        window.modalMessage({
            title: 'Erro ao deletar produto',
            description: 'Id do produto não informado',
            type: 'error',
        });
        return false;
    }

    document.getElementById(`deleteProduct_${id}`).classList.add('disabled');

    if (confirm('Tem certeza que deseja deletar este produto?')) {
        showLoading(true);
        fetch(`products/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        }).then(response => response.json()).then(data => {
            showLoading(false);
            document.getElementById(`deleteProduct_${id}`).classList.remove('disabled');
            if (data?.status === 200) {
                window.modalMessage({
                    title: 'Produto deletado com sucesso',
                    description: data.message,
                    type: 'success',
                });
                document.getElementById(`product_${id}`).remove();
            } else {
                window.modalMessage({
                    title: 'Erro ao deletar produto',
                    description: data?.message ?? 'Ocorreu um erro ao deletar o produto',
                    type: 'error',
                });
            }
        }).catch(error => {
            window.modalMessage({
                title: 'Erro ao deletar produto',
                description: 'Ocorreu um erro ao deletar o produto',
                type: 'error',
            });
        });
    }
}; 