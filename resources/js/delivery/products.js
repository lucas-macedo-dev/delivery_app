'use strict';

import * as bootstrap from 'bootstrap';

window.onload = function () {

};

window.readURL = function (input) {
    if (input.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("category-img-tag").setAttribute('src', e.target.result);
        };
        document.getElementById("image_preview").classList.remove("d-none");
        reader.readAsDataURL(input.target.files[0]);
    }
};

document.getElementById("product_image").addEventListener(`change`, function (event) {
    readURL(event);
});

window.closeProductModal = function () {
    bootstrap.Modal.getOrCreateInstance('#products_modal').hide();
    for (let i = 0; i < document.querySelectorAll('.btn_close_modal').length; i++) {
        document.querySelectorAll('.btn_close_modal')[i].addEventListener('click', function (event) {
            document.getElementById('image_preview').classList.add("d-none");
            document.getElementById('category-img-tag').setAttribute('src', '');
        });
    }
};


document.getElementById('btn_create_prd').addEventListener('click', function (event) {
    event.preventDefault();
    let data = new FormData();
    let name = document.getElementById('product_name');
    let price = document.getElementById('product_price');
    let stock = document.getElementById('product_stock');
    let image = document.getElementById('product_image');
    let available = document.getElementById('product_available');
    let unitMeasure = document.getElementById('unit_measure');

    data.append('name', name.value);
    data.append('price', price.value);
    data.append('stock', stock.value);
    data.append('image', image.files[0]);
    data.append('available', available.checked);
    data.append('unit_measure', unitMeasure.value);

    let headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    let options = {
        method: 'POST',
        headers: headers,
        body: data
    };

    fetch(`./new_product`, options).then(function (response) {
        response.json().then(function (retorno) {
            if (retorno) {
                if (retorno?.status === 200 && retorno?.data) {
                    closeProductModal();
                    bootstrap.Modal.getOrCreateInstance('#products_modal').hide();
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
        });
    });
});

let products = document.querySelectorAll('.btn_edit_product');
for (let product of products) {
    product.addEventListener('click', async (event) => {
        let productInfos = await getProduct(product.id.replace('btn_edit_', ''));

        if (!productInfos) {
            window.modalMessage({
                title: 'Erro ao buscar produto',
                description: 'Ocorreu um erro ao buscar o produto',
                type: 'error',
            });
            return false;
        }
        bootstrap.Modal.getOrCreateInstance('#products_modal').hide();
    });
}


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

window.deleteProduct = function (id) {
    if (!id) {
        window.modalMessage({
            title: 'Erro ao deletar produto',
            description: 'Id do produto não informado',
            type: 'error',
        });
        return false;
    }

    if (confirm('Tem certeza que deseja deletar este produto?')) {
        fetch(`products/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        }).then(response => response.json()).then(data => {
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