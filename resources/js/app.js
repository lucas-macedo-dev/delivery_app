import * as bootstrap from 'bootstrap';

import Alpine from 'alpinejs';

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
