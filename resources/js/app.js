import * as bootstrap from 'bootstrap';

import Alpine from 'alpinejs';

window.bootstrap = bootstrap; // 🔑 deixa disponível globalmente

window.Alpine = Alpine;
Alpine.start();

/**
 * Headers automatically applied to all requests
 * @type {Headers}
 */
window.ajax_headers = new Headers({
    'Content-Type'    : 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
});

/**
 * Show a modal with a custom message
 * @author Douglas Vicentini Ferreira
 * @param {string} params.title Title for the modal message.
 * @param {string} params.description Message for the modal body.
 * @param {string} params.type Type of the message to be displayed. Info, success, warning or error. Default: info.
 * @param {number} params.time Time for the modal to close.
 * @param {string} params.buttonText Text for the modal footer button.
 * @param {string} params.buttonId Id for the modal footer button.
 * @param {Function} params.callback Callback function.
 **/
window.modalMessage = function(params = {}) {
    let modalName = 'modalMessage';
    if (!params.title || !params.description) {
        console.error('Please inform title and description');
        return false;
    }

    switch (params.type) {
        case 'success':
            params.title = '<i class="fa-solid fa-circle-check text-success"></i> ' + params.title;
            break;
        case 'warning':
            params.title = '<i class="fa-solid fa-exclamation-triangle text-warning"></i> ' + params.title;
            break;
        case 'error':
            params.title = '<i class="fa-solid fa-circle-xmark text-danger"></i> ' + params.title;
            break;
        case 'info':
            params.title = '<i class="fa-solid fa-circle-info text-info"></i> ' + params.title;
            break;
    }

    document.getElementById('modalMessageHeaderTitle').innerHTML = params.title;
    document.getElementById('modalMessageBody').innerHTML        = params.description;

    bootstrap.Modal.getOrCreateInstance(`#${modalName}`).show();

    if (params.buttonText && params.buttonId) {
        document.getElementById('modalMessageBody').innerHTML += `<button id=${params.buttonId} class="btn btn-${params.type} w-100 my-4">${params.buttonText}</button>`;
    }

    if (typeof params.time === 'number') {
        setTimeout(() => {
            bootstrap.Modal.getOrCreateInstance(`#${modalName}`).hide();
        }, params.time);
    }

    if (typeof params.callback === 'function') {
        if (typeof params.time === 'number') {
            setTimeout(() => {
                params.callback();
            }, params.time);
        } else {
            params.callback();
        }
    }
};

/**
 * Format phone number
 * @param {HTMLElement} element
 */
window.formatPhoneNumber = function(element) {
    if (!element instanceof HTMLElement) {
        return 'Informe um elemento HTML válido';
    }
    let x         = element.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    element.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
};
for (const phone of document.querySelectorAll('.phone_number')) {
    phone.addEventListener('keyup', (e) => {
        formatPhoneNumber(phone);
    });
}

/**
 * Função para exibir ou esconder o ícone de carregamento de forma simplificada
 * @author Douglas Vicentini Ferreira
 * @param {boolean} loading Indica se irá exibir ou esconder o ícone de carregamento
 */
window.showLoading = function(loading = true) {
    document.activeElement.blur();
    if (loading) {
        document.body.classList.add('pe-none');
        document.querySelector('.main-loading').classList.remove('d-none');
    } else {
        document.body.classList.remove('pe-none');
        document.querySelector('.main-loading').classList.add('d-none');
    }
};

// Aparecimento do botão de rolagem para o topo
window.addEventListener('scroll', function(event) {
    let scroll_distance = this.scrollY;
    let back_to_top     = document.querySelector('.back_to_top');

    if (scroll_distance > 100) {
        back_to_top?.classList.remove('d-none');
    } else {
        back_to_top?.classList.add('d-none');
    }
});

window.animateValue = function(id, start, end, duration) {
    let obj       = document.getElementById(id);
    let range     = end - start;
    let current   = start;
    let increment = end > start ? 1 : -1;
    let stepTime  = Math.abs(Math.floor(duration / range));
    let timer     = setInterval(function() {
        current += increment;
        obj.innerHTML = current;
        if (current == end) {
            clearInterval(timer);
        }
    }, stepTime);
};

document.querySelector('.back_to_top')?.addEventListener('click', function(event) {
    event.preventDefault();
    window.scrollTo({top: 0, behavior: 'smooth'});
});

// Habilitando todos os tooltips
let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function(tooltip_trigger_element) {
    return new bootstrap.Tooltip(tooltip_trigger_element);
});

//add hovered class in selected list item
let list          = document.querySelectorAll('.navigation li');
let listLink      = document.querySelectorAll('.navigation li a');
window.activeLink = function() {
    list.forEach((item) =>
        item.classList.remove('hovered'));
    this.classList.add('hovered');
};

/**
 * Paginate the registers
 * @param {number} params.page Page to be shown
 * @param {number} params.total Total amount of registers
 * @param {number} params.max Maximum of register to be shown
 * @param {number} params.qtt Quantity of pagination items
 * @param {string} params.id Pagination element id
 * @param {string} params.callback Callback function
 */
window.pagination = function(params = {}) {
    if (!params.qtt) {
        params.qtt = 5;
    }
    if (params.total > params.max) {
        document.getElementById(params.id).classList.remove('d-none');

        let pages = Math.ceil(params.total / params.max);
        let first = Math.ceil(params.page - params.qtt / 2);

        if (first <= 0 && first < params.qtt) {
            first = 1;
        }

        let last = first + params.qtt - 1;

        if (last > pages) {
            last = pages;
        }

        let previous   = params.page - 1;
        let next       = params.page + 1;
        let pagination = '';

        if (pages > 1 && params.id) {
            if (previous > 0) {
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100' onclick='${params.callback}(1);'
                                aria-label='Previous'>
                            <i class='fa-solid fa-angle-double-left mt-1'></i>
                        </button>
                    </li>
                `;
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100' onclick='${params.callback}(${previous})'
                                aria-label='Previous'>
                            <i class='fa-solid fa-angle-left mt-1'></i>
                        </button>
                    </li>
                `;
            } else {
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100 disabled' aria-label='Previous'>
                            <i class='fa-solid fa-angle-double-left mt-1'></i>
                        </button>
                    </li>
                `;
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100 disabled' aria-label='Previous'>
                            <i class='fa-solid fa-angle-left mt-1'></i>
                        </button>
                    </li>
                `;
            }
            for (let i = first; i <= last; i++) {
                if (i !== params.page) {
                    pagination += `
                        <li class='page-item'>
                            <button type='button' class='page-link h-100' onclick='${params.callback}(${i})'>${i}</button>
                        </li>
                    `;
                } else {
                    pagination += `
                        <li class='page-item active'>
                            <button type='button' class='page-link h-100' id='pagina_atual'
                                    onclick='${params.callback}(${i})'>${i}</button>
                        </li>
                    `;
                }
            }
            if (next <= pages) {
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100' onclick='${params.callback}(${next})'
                                aria-label='Next'>
                            <i class='fa-solid fa-angle-right mt-1'></i>
                        </button>
                    </li>
                `;
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100' onclick='${params.callback}(${pages})'
                                aria-label='Next'>
                            <i class='fa-solid fa-angle-double-right mt-1'></i>
                        </button>
                    </li>
                `;
            } else {
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100 disabled' aria-label='Previous'>
                            <i class='fa-solid fa-angle-right mt-1'></i>
                        </button>
                    </li>
                `;
                pagination += `
                    <li class='page-item'>
                        <button type='button' class='page-link h-100 disabled' aria-label='Next'>
                            <i class='fa-solid fa-angle-double-right mt-1'></i>
                        </button>
                    </li>
                `;
            }
            document.getElementById(params.id).innerHTML = pagination;
        }
    } else {
        document.getElementById(params.id)?.classList.add('d-none');
    }
};


window.showFormErrors = function (errors) {
    Object.keys(errors).forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            const feedback = input.parentElement.querySelector('.invalid-feedback');

            input.classList.add('is-invalid');
            if (feedback) {
                feedback.textContent = errors[field][0];
            }
        }
    });
}

window.clearFormErrors = function () {
    document.querySelectorAll('.is-invalid').forEach(input => {
        input.classList.remove('is-invalid');
    });
}

window.formatCurrency = function (value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
}

window.formatDate = function (dateString) {
    if (!dateString) return 'N/A';

    try {
        let date;

        if (dateString.includes('T')) {
            date = new Date(dateString);
        } else {
            date = new Date(dateString + 'T00:00:00');
        }

        if (isNaN(date.getTime())) {
            return 'Data inválida';
        }

        return date.toLocaleDateString('pt-BR');
    } catch (error) {
        console.error('Error formatting date:', error, dateString);
        return 'Data inválida';
    }
}
