'use strict';

window.onload = () => {
    getAllCustomers();
};


window.openCustomerModal = function (customer = null) {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    const title = document.getElementById('customerModalTitle');

    if (customer) {
        document.getElementById('updateCustomer').classList.remove('d-none');
        document.getElementById('saveCustomer').classList.add('d-none');
        title.textContent = 'Editar Cliente';
        document.getElementById('customerId').value = customer.id;
        document.getElementById('customerName').value = customer.name;
        document.getElementById('customerPhone').value = customer.phone;
        document.getElementById('customerCpf').value = customer.cpf;
    } else {
        document.getElementById('updateCustomer').classList.add('d-none');
        document.getElementById('saveCustomer').classList.remove('d-none');
        title.textContent = 'Cadastrar Cliente';
        document.getElementById('customerForm').reset();
        document.getElementById('customerId').value = '';
    }

    modal.show();
};

window.saveCustomer = async function (action = 'create') {
    const id = document.getElementById('customerId').value;
    const name = document.getElementById('customerName').value;
    const phone = document.getElementById('customerPhone').value;
    const cpf = document.getElementById('customerCpf').value;

    let route = 'customers/new_customer';
    let customerId = document.getElementById('customerId').value;
    if (action !== 'create') {
        route = `customers/edit/${customerId}`;
    }

    let options = {
        method: 'POST',
        headers: window.ajax_headers,
        body: JSON.stringify({
            id,
            name,
            phone,
            cpf
        }),
    };

    showLoading(true);
    let response = await fetch(`${route}`, options);
    let retorno = await response.json();

    if (retorno) {
        if (retorno?.status === 200 && retorno?.data) {
            bootstrap.Modal.getOrCreateInstance('#customerModal').hide();
            window.modalMessage({
                title: retorno.message,
                description: retorno.message,
                type: 'success',
            });
            if (action !== 'create') {
                editCustomerLine(retorno.data);
            }
            buildCustomerLine(retorno.data);
        } else {
            let alerts = retorno.errors;
            let message = '';
            for (let key in alerts) {
                if (alerts.hasOwnProperty(key)) {
                    message += `${key}: ${alerts[key].join(', ')}<br>`;
                }
            }
            window.modalMessage({
                title: 'Erro ao cadastrar cliente',
                description: message,
                type: 'error',
            });
        }
    } else {
        window.modalMessage({
            title: 'Erro ao cadastrar cliente',
            description: 'Ocorreu um erro ao tentar cadastrar o cliente. Por favor, tente novamente mais tarde.',
            type: 'error',
        });
    }
    showLoading(false);

    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
};



window.buildCustomerLine = function (customer) {
    document.getElementById('customersTableBody').innerHTML += `
        <tr id="customer_${customer.id}" class="align-middle">
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                        ${customer.id}
                    </div>
                    <div class="d-block d-md-none ms-2">
                        <div class="fw-bold" id="custormer_${customer.id}_name_mobile">${customer.name}</div>
                        <div class="small text-muted" id="custormer_${customer.id}_phone_mobile">${customer.phone ?? '-'}</div>
                        <div class="small text-muted" id="custormer_${customer.id}_cpf_mobile">${customer.cpf ?? '-'}</div>
                    </div>
                </div>
            </td>
            <td class="d-none d-md-table-cell" id="custormer_${customer.id}_name">${customer.name}</td>
            <td class="d-none d-md-table-cell" id="custormer_${customer.id}_phone">${customer.phone ?? '-'}</td>
            <td class="d-none d-md-table-cell" id="custormer_${customer.id}_cpf">${customer.cpf ?? '-'}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editCustomer(${customer.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCustomer(${customer.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
};

window.getAllCustomers = async function (page = 1) {
    let customers = await fetch('./customers/showAll?page=' + page);
    let response = await customers.json();
    if (response?.status === 200 && response?.data?.customers) {
        buildCustomersTable(response.data.customers);
    } else {
        document.getElementById('customersTableBody').innerHTML = `
            <tr>
                <td colspan="5" class="text-center"> Nenhum cliente encontrado.
                </td>
            </tr>
        `;
    }

    window.pagination({
        page: page,
        total: response?.data?.meta?.total,
        max: response?.data?.meta?.per_page,
        qtt: 5,
        id: `pagination`,
        callback: `getAllCustomers`
    });
};

window.buildCustomersTable = function (customers) {

    if (!customers || customers.length === 0) {
        document.getElementById('customersTableBody').innerHTML = `
            <tr>
                <td colspan="5" class="text-center"> Nenhum cliente encontrado.
                </td>
            </tr>
        `;
        return;
    }
    document.getElementById('customersTableBody').innerHTML = '';
    customers.map((customer) => buildCustomerLine(customer));
};

window.editCustomer = async function (id) {
    const customer = await getCustomer(id);

    if (!customer) {
        window.modalMessage({
            title: 'Error',
            description: 'Produto não encontrado',
            type: 'error',
        });
        return;
    }
    openCustomerModal(customer);
};

window.deleteCustomer = function (id) {
    if (!id) {
        window.modalMessage({
            title: 'Erro ao deletar cliente',
            description: 'Id do cliente não localizado',
            type: 'error',
        });
        return;
    }

    if (confirm('Tem certeza que deseja deletar este cliente?')) {
        showLoading(true);
        fetch(`./customers/delete/${id}`, {
            method: 'DELETE',
            headers: window.ajax_headers,
        })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                if (data.status === 200) {
                    window.modalMessage({
                        title: 'Cliente deletado com sucesso',
                        description: data.message,
                        type: 'success',
                    });
                    document.getElementById(`customer_${id}`).remove();
                } else {
                    window.modalMessage({
                        title: 'Erro ao deletar cliente',
                        description: data.message,
                        type: 'error',
                    });
                }
            })
            .catch(error => {
                showLoading(false);
                window.modalMessage({
                    title: 'Erro ao deletar cliente',
                    description: 'Ocorreu um erro ao tentar deletar o cliente. Por favor, tente novamente mais tarde.',
                    type: 'error',
                });
            });
    }
};

window.editCustomerLine = function (newClientData) {
    if (!newClientData || !newClientData.id) {
        window.modalMessage({
            title: 'Erro ao atualizar cliente',
            description: 'Dados do cliente inválidos',
            type: 'error',
        });
        return;
    }
    const id = newClientData.id;
    const customer = document.getElementById(`customer_${newClientData.id}`);
    if (!customer) {
        window.modalMessage({
            title: 'Erro ao buscar cliente',
            description: 'Cliente nao encontrado',
            type: 'error',
        });
        return;
    }
    customer.querySelector(`#custormer_${id}_name`).textContent = newClientData.name;
    customer.querySelector(`#custormer_${id}_phone`).textContent = newClientData.phone;
    customer.querySelector(`#custormer_${id}_cpf`).textContent = newClientData.cpf;

    customer.querySelector(`#custormer_${id}_name_mobile`).textContent = newClientData.name;
    customer.querySelector(`#custormer_${id}_phone_mobile`).textContent = newClientData.phone;
    customer.querySelector(`#custormer_${id}_cpf_mobile`).textContent = newClientData.cpf;
};

window.getCustomer = async function (id) {
    if (!id) {
        window.modalMessage({
            title: 'Erro ao buscar cliente',
            description: 'Id do cliente não localizado',
            type: 'error',
        });
    }

    let customer = await fetch(`./customers/show/${id}`);
    let response = await customer.json();

    if (response?.status === 200 && response?.data) {
        return response.data;
    } else {
        window.modalMessage({
            title: 'Erro ao buscar cliente',
            description: response?.message ?? 'Cliente não encontrado',
            type: 'error',
        });
    }
    return false;
};
