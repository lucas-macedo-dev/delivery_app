window.onload = async function () {
    await searchData();
}

window.loadChartData = async function (period = 'week') {
    showLoading(true);
    const ctx = document.getElementById('salesChart')
    let startDate = document.getElementById('startDate').value;
    let endDate   = document.getElementById('endDate').value;
    let response  = await fetch(`./home/salesPerDayOfWeek?startDate=${startDate}&endDate=${endDate}&period=${period}`, {
        headers: window.ajax_headers
    });

    let data = await response.json();

    let deParaDiasDaSemana = {
        1: 'Domingo',
        2: 'Segunda',
        3: 'Terça',
        4: 'Quarta',
        5: 'Quinta',
        6: 'Sexta',
        7: 'Sábado'
    };

    let salesData = [0, 0, 0, 0, 0, 0, 0];
    if (data.status === 200 && data.data) {
        Object.entries(data.data).forEach(([day_of_week, total_sales]) => {
            let diaSemana    = deParaDiasDaSemana[day_of_week];
            let index        = Object.values(deParaDiasDaSemana).indexOf(diaSemana);
            salesData[index] = total_sales;
        });
    } else {
        modalMessage({
            title      : 'Erro ao buscar dados do gráfico',
            description: data.message || 'Erro desconhecido',
            type       : 'error',
        });
    }

    if(Chart.getChart(ctx)) {
        Chart.getChart(ctx)?.destroy()
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels  : ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
            datasets: [
                {
                    label      : 'Valor vendido no dia',
                    data       : salesData,
                    borderWidth: 1
                }
            ],

        }
    });
    showLoading(false);
}

window.loadExpensesChart = async function (period = 'week') {
    showLoading(true);
    const ctx     = document.getElementById('expensesChart')
    let startDate = document.getElementById('startDate').value;
    let endDate   = document.getElementById('endDate').value;
    let response  = await fetch(`./home/expensesPerCategory?startDate=${startDate}&endDate=${endDate}`, {
        headers: window.ajax_headers
    });

    if(Chart.getChart(ctx)) {
        Chart.getChart(ctx)?.destroy()
    }

    let data = await response.json();
    let labels = [];
    let values = [];
    if (data.status === 200 && data.data) {
        labels = data.data.filter(item => item.description).map(item => item.description);
        values = data.data.filter(item => item.expenses_sum_value).map(item => item.expenses_sum_value);
        console.log(labels, values);
    } else {
        modalMessage({
            title      : 'Erro ao buscar dados do gráfico',
            description: data.message || 'Erro desconhecido',
            type       : 'error',
        });
    }

    if (data.data.length === 0) {
        labels = ['Sem despesas, continue assim!'];
        values = [1];
    }

    const dataChart = {
        labels  : labels,
        datasets: [
            {
                label          : 'Valor das Despesas',
                data           : values,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset    : 2
            }
        ]
    };

    const config = {
        type: 'pie',
        data: dataChart,
        options: {
            maintainAspectRatio: false
        }
    };

    new Chart(ctx, config);
    showLoading(false);
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
        await loadChartData();
        await loadExpensesChart();
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
