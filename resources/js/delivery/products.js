'use strict';

const baseUrl  = window.location.origin;
let categories = {};

window.onload = () => {
    getAllProducts();
    loadCategories();
};

window.getProduct = async function (id) {
    if (!id) {
        window.modalMessage({
            title      : 'Erro ao buscar produto',
            description: 'Id do produto não informado',
            type       : 'error',
        });
    }

    let product  = await fetch(`./products/show/${id}`);
    let response = await product.json();

    if (response?.status === 200 && response?.data) {
        return response.data;
    } else {
        window.modalMessage({
            title      : 'Erro ao buscar produto',
            description: response?.message ?? 'Produto não encontrado',
            type       : 'error',
        });
    }
    return false;
};

window.loadCategories = async function () {
    let categories = await fetch('./categories/showAll');
    let response   = await categories.json();

    if (response?.status === 200 && response?.data) {
        let categoriesOption = document.getElementById('category');
        response.data.forEach(category => {
            categoriesOption.innerHTML += `<option value="${category.id}" data-usa-estoque="${category.need_stock}">${category.description}</option>`;
        });
    } else {
        window.modalMessage({
            title      : 'Erro ao buscar categorias disponíveis',
            description: response?.message ?? 'Categorias não encontradas',
            type       : 'error',
        });
    }
};

window.getAllProducts = async function (page = 1) {
    let products = await fetch('./products/showAll?page=' + page);
    let response = await products.json();

    if (response?.status === 200 && response?.data?.products) {
        buildProductsGrid(response.data.products);
    } else {
        buildProductsGrid([]);
    }

    pagination({
        page    : page,
        total   : response?.data?.meta?.total,
        max     : response?.data?.meta?.per_page,
        qtt     : 5,
        id      : `pagination`,
        callback: `getAllProducts`
    });

};

window.buildProductsGrid = function (products) {
    let html                                         = ``;
    document.getElementById(`productList`).innerHTML = ``;

    if (!products || products.length === 0) {
        document.getElementById(`productList`).innerHTML = `
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    Nenhum produto encontrado.
                </div>
            </div>
        `;
        return;
    }
    products.forEach(product => {
        html += buildProductCard(product);
    });
};

window.buildProductCard = function (productData) {
    let cards = document.querySelectorAll('.card');
    if (cards.length === 0) {
        document.getElementById(`productList`).innerHTML = ``;
    }

    document.getElementById(`productList`).innerHTML += `
        <div class="col-12 mb-3" id="product_${productData.id}">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-3 col-sm-2 col-md-1">
                            <div class="d-flex justify-content-center">
                                <span class="d-flex align-items-center justify-content-center rounded-circle"
                                      style="width: 50px; height: 50px; background-color: #FDF5CB; font-size: 1.5rem;">
                                    ${productData.icon}
                                </span>
                            </div>
                        </div>
                        <div class="col-9 col-sm-4 col-md-3">
                            <h5 class="card-title mb-0 text-truncate" title="${productData.name}">
                                ${productData.name}
                            </h5>
                        </div>
                        <div class="col-5 col-sm-6 col-md-4">
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2"><i class="fa-solid fa-money-bill-wave"></i>&nbsp;Preço:</span>
                                    <span>R$ ${parseFloat(productData.price).toFixed(2)}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2"><i class="fa-solid fa-boxes-stacked"></i>&nbsp;Estoque:</span>
                                    <span>${ productData.need_stock === 1 ? productData.stock + ' ' + productData.unit_measure : '-' }</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2 text-center">
                            <span class="badge ${productData.available ? 'bg-success' : 'bg-danger'} w-100">
                                <i class="bi ${productData.available ? 'bi-check-circle' : 'bi-x-circle'} me-1"></i>
                                ${productData.available ? 'Disponível' : 'Indisponível'}
                            </span>
                        </div>
                        <div class="col-3 col-sm-8 col-md-2">
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm flex-grow-1"
                                        onclick="editProduct(${productData.id})"
                                        title="Editar produto">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-sm-inline">Editar</span>
                                </button>
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="deleteProduct(${productData.id})"
                                        title="Excluir produto">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-sm-inline">Excluir</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
};

window.openProductModal = function (product = null) {
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const title = document.getElementById('productModalTitle');

    if (product) {
        document.getElementById('updateProduct').classList.remove('d-none');
        document.getElementById('saveProduct').classList.add('d-none');
        title.textContent                                   = 'Edit Product';
        document.getElementById('productId').value          = product.id;
        document.getElementById('productName').value        = product.name;
        document.getElementById('productValue').value       = product.price;
        document.getElementById('productStock').value       = product.stock;
        document.getElementById('productUnit').value        = product.unit_measure;
        document.getElementById('productAvailable').checked = product.available;
        document.getElementById('category').value           = product.category;
    } else {
        document.getElementById('updateProduct').classList.add('d-none');
        document.getElementById('saveProduct').classList.remove('d-none');
        title.textContent = 'Add New Product';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
    }

    modal.show();
};

window.closeProductModal = function () {
    bootstrap.Modal.getOrCreateInstance('#productModal').hide();
};

window.saveProduct = async function (action = 'create') {
    const name        = document.getElementById('productName').value;
    const price       = parseFloat(document.getElementById('productValue').value);
    const stock       = parseInt(document.getElementById('productStock').value);
    const unitMeasure = document.getElementById('productUnit').value;
    const category    = document.getElementById('category').value;
    const available   = document.getElementById('productAvailable').checked;

    const data = new FormData();
    data.append('name', name);
    data.append('price', price);
    data.append('stock', stock);
    data.append('available', available);
    data.append('unit_measure', unitMeasure);
    data.append('category', category);

    let headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    let route     = 'products/new_product';
    let productId = document.getElementById('productId').value;
    if (action !== 'create') {
        route = `products/edit/${productId}`;
    }

    let options = {
        method : 'POST',
        headers: headers,
        body   : data
    };

    showLoading(true);
    let response = await fetch(`${route}`, options);
    let retorno  = await response.json();

    if (retorno) {
        if (retorno?.status === 200 && retorno?.data) {
            closeProductModal();
            window.modalMessage({
                title      : retorno.message,
                description: retorno.message,
                type       : 'success',
            });
            if (action !== 'create') {
                document.getElementById(`product_${productId}`).remove();
            }
            buildProductCard(retorno.data);
        } else {
            let alerts  = retorno.errors;
            let message = '';
            for (let key in alerts) {
                if (alerts.hasOwnProperty(key)) {
                    message += `${key}: ${alerts[key].join(', ')}<br>`;
                }
            }
            window.modalMessage({
                title      : 'Erro ao salvar produto',
                description: message,
                type       : 'error',
            });
        }
    } else {
        window.modalMessage({
            title      : 'Erro ao salvar produto',
            description: 'Ocorreu um erro ao salvar o produto',
            type       : 'error',
        });
    }
    showLoading(false);

    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
};

window.editProduct = async function (id) {
    const product = await getProduct(id);

    if (!product) {
        window.modalMessage({
            title      : 'Error',
            description: 'Produto não encontrado',
            type       : 'error',
        });
        return;
    }
    openProductModal(product);
};

window.deleteProduct = function (id) {
    if (!id) {
        window.modalMessage({
            title      : 'Erro ao deletar produto',
            description: 'Id do produto não informado',
            type       : 'error',
        });
        return false;
    }

    document.getElementById(`deleteProduct_${id}`).classList.add('disabled');

    if (confirm('Tem certeza que deseja deletar este produto?')) {
        showLoading(true);
        fetch(`products/delete/${id}`, {
            method : 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        }).then(response => response.json()).then(data => {
            showLoading(false);
            document.getElementById(`deleteProduct_${id}`).classList.remove('disabled');
            if (data?.status === 200) {
                window.modalMessage({
                    title      : 'Produto deletado com sucesso',
                    description: data.message,
                    type       : 'success',
                });
                document.getElementById(`product_${id}`).remove();
            } else {
                window.modalMessage({
                    title      : 'Erro ao deletar produto',
                    description: data?.message ?? 'Ocorreu um erro ao deletar o produto',
                    type       : 'error',
                });
            }
        }).catch(error => {
            window.modalMessage({
                title      : 'Erro ao deletar produto',
                description: 'Ocorreu um erro ao deletar o produto',
                type       : 'error',
            });
        });
    }
};
