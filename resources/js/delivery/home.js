window.onload = () => {
    searchData();
}

window.searchData = async function () {
    showLoading(true);
    let startDate = document.getElementById('startDate').value;
    let endDate   = document.getElementById('endDate').value;

    let response = await fetch(`./home/searchData?startDate=${startDate}&endDate=${endDate}`, {
        headers: window.ajax_headers
    });

    let data = await response.json();
    if (data.status === 200 && data.data) {
        document.getElementById('total_customers').textContent = data.data.customers;
        document.getElementById('total_orders').textContent    = data.data.orders;
        document.getElementById('total_amount').textContent    = new Intl.NumberFormat("sp-SP", {
                style   : "currency",
                currency: "BRL"
            }
        ).format(data.data.amount || 0);
        document.getElementById('total_expenses').textContent  = new Intl.NumberFormat("sp-SP", {
                style   : "currency",
                currency: "BRL"
            }
        ).format(data.data.expenses || 0);

        buildMostSaledList(data.data.most_saled_product);
    } else {
        modalMessage({
            title      : 'Erro ao buscar dados',
            description: data.message || 'Erro desconhecido',
            type       : 'error',
        });
    }
    showLoading(false);
}

window.buildMostSaledList = function (products) {
    console.log(products)
    let html = '';

    if (products.length > 0) {
        for (let i = 0; i < products.length; i++) {
            html += `
            <div class="d-flex align-items-center p-2 shadow mb-3">
                <div class="flex-shrink-0">
                    ${products[i].categories.icon}
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">${products[i].name}</h6>
                    <small class="text-muted">${products[i].total_quantity} vendas</small>
                </div>
                <div class="flex-shrink-0">
                    <span class="badge bg-secondary">${i + 1}º</span>
                </div>
            </div>
        `;
        }
    } else {
        html = `<p class="text-muted mb-0">Nenhum produto vendido nesse período.</p>`;
    }

    document.getElementById('most_saled_product').innerHTML = html;

}
