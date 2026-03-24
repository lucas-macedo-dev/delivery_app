import './bootstrap';
import './old_app.js';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import * as bootstrap from 'bootstrap';

import Alpine from 'alpinejs';

window.bootstrap = bootstrap;

window.Alpine = Alpine;
Alpine.start();

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.jsx',{eager: true})
        return pages[`./Pages/${name}.jsx`]
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});

window.React = {
    modalMessage: window.modalMessage,
    showLoading: window.showLoading,
    formatCurrency: window.formatCurrency,
    formatDate: window.formatDate,
    formatPhoneNumber: window.formatPhoneNumber,
    showFormErrors: window.showFormErrors,
    clearFormErrors: window.clearFormErrors,
};
