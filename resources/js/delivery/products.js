'use strict';

import * as bootstrap from 'bootstrap';

window.onload = function () {

}

window.readURL = function (input) {
    if (input.target.files[0]) {
        let reader    = new FileReader();
        reader.onload = function (e) {
            document.getElementById("category-img-tag").setAttribute('src', e.target.result);
        }
        document.getElementById("image_preview").classList.remove("d-none");
        reader.readAsDataURL(input.target.files[0]);
    }
}

document.getElementById("product_image").addEventListener(`change`, function (event) {
    readURL(event);
});

window.closeProductModal = function () {
    bootstrap.Modal.getOrCreateInstance('#products_modal').hide();
    for (let i = 0; i < document.querySelectorAll('.btn_close_modal').length; i++) {
        document.querySelectorAll('.btn_close_modal')[i].addEventListener('click', function (event) {
            document.getElementById('image_preview').classList.add("d-none");
            document.getElementById('category-img-tag').setAttribute('src', '');
        })
    }
}


document.getElementById('btn_create_prd').addEventListener('click', function (event) {
    event.preventDefault();
    let data = new FormData();
    data.append('name', document.getElementById('product_name').value);
    data.append('price', document.getElementById('product_price').value);
    data.append('description', document.getElementById('product_description').value);
    data.append('image', document.getElementById('product_image').files[0]);
    data.append('available', document.getElementById('product_available').checked);


    let headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    let options = {
        method : 'POST',
        headers: headers,
        body   : data
    }

    fetch(`./new_product`, options).then(function (response) {
        if (!response.ok) {
            console.log(response);
            return false;
        }
        response.json().then(function (retorno) {
            if (retorno) {
                if (retorno?.status === 200 && retorno?.data) {
                    alert('aaaaaaaaaaaaaaaaaaaa');
                    closeProductModal();
                    bootstrap.Modal.getOrCreateInstance('#products_modal').hide();
                }
            } else {
                window.alert('Error');
            }
        });
    });
})

let products = document.querySelectorAll('.btn_edit_product');
for (let product of products) {
    product.addEventListener('click', async (event) => {
        let productInfos =  await getProduct(product.id.replace('btn_edit_', ''));

        if (!productInfos) {
            window.modalMessage({
                title      : 'Erro ao buscar produto',
                description: 'Ocorreu um erro ao buscar o produto',
                type       : 'error',
            })
            return false;
        }
        bootstrap.Modal.getOrCreateInstance('#products_modal').hide();
    })
}


window.getProduct = async function (id) {
    if (!id) {
        window.modalMessage({
            title      : 'Erro ao buscar produto',
            description: 'Id do produto não informado',
            type       : 'error',
        })
    }

    let product  = await fetch(`products/${id}`);
    let response = await product.json();

    if (response?.status === 200 && response?.data) {
        return response.data;
    } else {
        window.modalMessage({
            title      : 'Erro ao buscar produto',
            description: response?.message ?? 'Produto não encontrado',
            type       : 'error',
        })
    }
    return false;
}
