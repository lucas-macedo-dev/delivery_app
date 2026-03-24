
/**
 * Helpers para usar funções do app.js dentro de componentes React
 */

export const modalMessage = (params) => {

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
    document.getElementById('modalMessageBody').innerHTML = params.description;

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


export const showLoading = (loading = true) => {
    document.activeElement?.blur?.();
    if (loading) {
        document.body.classList.add('pe-none');
        document.querySelector('.main-loading')?.classList.remove('d-none');
    } else {
        document.body.classList.remove('pe-none');
        document.querySelector('.main-loading')?.classList.add('d-none');
    }
};

export const formatCurrency = (value) => {
    if (window.formatCurrency) {
        return window.formatCurrency(value);
    }
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
};

export const formatDate = (dateString) => {
    if (window.formatDate) {
        return window.formatDate(dateString);
    }

    if (!dateString) return 'N/A';

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    } catch (error) {
        return 'Data inválida';
    }
};

export const showFormErrors = (errors) => {
    if (window.showFormErrors) {
        window.showFormErrors(errors);
    }
};

export const clearFormErrors = () => {
    if (window.clearFormErrors) {
        window.clearFormErrors();
    }
};

export const formatPhoneNumber = (element) => {
    if (window.formatPhoneNumber) {
        window.formatPhoneNumber(element);
    }
};

export const animateValue = (id, start, end, duration) => {
    if (window.animateValue) {
        window.animateValue(id, start, end, duration);
    }
};

export const pagination = (params) => {
    if (window.pagination) {
        window.pagination(params);
    }
};

// Hook React para usar os helpers
export const useLegacyHelpers = () => {
    return {
        modalMessage,
        showLoading,
        formatCurrency,
        formatDate,
        showFormErrors,
        clearFormErrors,
        formatPhoneNumber,
        animateValue,
        pagination,
    };
};
