import { Head, router, useForm, usePage } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import { useState, useEffect } from 'react';
import { formatCurrency, formatDate, modalMessage, showLoading } from '@/Utils/legacyHelpers';

export default function ShoppingListIndex() {
    const { lists, stats } = usePage().props;
    const [showCreateModal, setShowCreateModal] = useState(false);

    const { data, setData, post, put, reset, errors } = useForm({
        name: '',
        description: '',
        items: [],
    });

    const handleCreateList = (e) => {
        e.preventDefault();
        showLoading(true);

        post('/delivery/shopping-lists', {
            onSuccess: () => {
                reset();
                setShowCreateModal(false);
                modalMessage({
                    title: 'Sucesso!',
                    description: 'Lista de compras criada com sucesso!',
                    type: 'success',
                    time: 2000
                });
            },
            onError: () => {
                modalMessage({
                    title: 'Erro',
                    description: 'Erro ao criar lista. Verifique os dados.',
                    type: 'error',
                    time: 3000
                });
            },
            onFinish: () => showLoading(false)
        });
    };

    const handleDeleteList = (id, name) => {
        modalMessage({
            title: 'Confirmar Exclusão',
            description: `Deseja realmente excluir a lista "${name}"? Esta ação não pode ser desfeita.`,
            type: 'warning',
            buttonText: 'Sim, excluir',
            buttonId: 'btn-delete-list',
            callback: () => {
                document.getElementById('btn-delete-list')?.addEventListener('click', () => {
                    showLoading(true);
                    router.delete(`/delivery/shopping-lists/${id}`, {
                        onSuccess: () => {
                            modalMessage({
                                title: 'Sucesso',
                                description: 'Lista excluída com sucesso!',
                                type: 'success',
                                time: 2000
                            });
                        },
                        onFinish: () => showLoading(false)
                    });
                });
            }
        });
    };

    const handleFilterStatus = (status) => {
        showLoading(true);
        router.get(`/delivery/shopping-lists/filterStatus/${status}`, {
            preserveState: true,
            onFinish: () => showLoading(false)
        });
    };


    const getStatusColor = (status) => {
        const colors = {
            pending: 'warning',
            in_progress: 'info',
            completed: 'success',
            cancelled: 'danger'
        };
        return colors[status] || 'secondary';
    };

    const getStatusLabel = (status) => {
        const labels = {
            pending: 'Pendente',
            in_progress: 'Em Andamento',
            completed: 'Concluída',
            cancelled: 'Cancelada'
        };
        return labels[status] || status;
    };

    return (
        <AppLayout>
            <Head title="Listas de Compras" />

            <div className="container-fluid">
                {/* Header */}
                <div className="row mb-4">
                    <div className="col-md-8">
                        <h2 className="mb-1">🛒 Listas de Compras</h2>
                        <p className="text-muted mb-0">Organize suas compras de insumos e produtos</p>
                    </div>
                    <div className="col-md-4 text-end">
                        <button
                            className="btn btn-primary"
                            onClick={() => setShowCreateModal(true)}
                        >
                            <i className="fas fa-plus me-2"></i>
                            Nova Lista
                        </button>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="row g-3 mb-4">
                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm h-100">
                            <div className="card-body">
                                <div className="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 className="text-muted mb-1">Total de Listas</h6>
                                        <h3 className="mb-0">{stats?.total || 0}</h3>
                                    </div>
                                    <div className="fs-1">📋</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm h-100">
                            <div className="card-body">
                                <div className="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 className="text-muted mb-1">Pendentes</h6>
                                        <h3 className="mb-0 text-warning">{stats?.pending || 0}</h3>
                                    </div>
                                    <div className="fs-1">⏳</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm h-100">
                            <div className="card-body">
                                <div className="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 className="text-muted mb-1">Em Andamento</h6>
                                        <h3 className="mb-0 text-info">{stats?.in_progress || 0}</h3>
                                    </div>
                                    <div className="fs-1">🛍️</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm h-100">
                            <div className="card-body">
                                <div className="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 className="text-muted mb-1">Valor Estimado</h6>
                                        <h3 className="mb-0 text-success">{formatCurrency(stats?.total_value || 0)}</h3>
                                    </div>
                                    <div className="fs-1">💰</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="row mb-4">
                    <div className="col-12 d-flex justify-content-center">
                        <div className="btn-group" role="group">
                            <button className="btn btn-sm btn-outline-primary active" onClick={() => handleFilterStatus('all')}>
                                <i className="fas fa-list me-2"></i>
                                Todas
                            </button>
                            <button className="btn btn-sm btn-outline-warning" onClick={() => handleFilterStatus('pending')}>
                                <i className="fas fa-clock me-2"></i>
                                Pendentes
                            </button>
                            <button className="btn btn-sm btn-outline-info" onClick={() => handleFilterStatus('in_progress')}>
                                <i className="fas fa-shopping-cart me-2"></i>
                                Em Andamento
                            </button>
                            <button className="btn btn-sm btn-outline-success" onClick={() => handleFilterStatus('completed')}>
                                <i className="fas fa-check-circle me-2"></i>
                                Concluídas
                            </button>
                        </div>
                    </div>
                </div>

                {/* Lists Grid */}
                <div className="row g-3">
                    {lists?.data?.length > 0 ? (
                        lists.data.map(list => (
                            <div key={list.id} className="col-md-6 col-lg-4">
                                <div className="card border-0 shadow-sm h-100 hover-shadow transition">
                                    <div className="card-header bg-white border-bottom">
                                        <div className="d-flex justify-content-between align-items-start">
                                            <div className="flex-grow-1">
                                                <h5 className="mb-1">{list.name}</h5>
                                                <span className={`badge bg-${getStatusColor(list.status)}`}>
                                                    {getStatusLabel(list.status)}
                                                </span>
                                            </div>
                                            <div className="dropdown">
                                                <button
                                                    className="btn btn-sm btn-light"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                >
                                                    <i className="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul className="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href={`/delivery/shopping-lists/${list.id}`}>
                                                            <i className="fas fa-eye me-2"></i>
                                                            Ver Detalhes
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href={`/delivery/shopping-lists/${list.id}/edit`}>
                                                            <i className="fas fa-edit me-2"></i>
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li><hr className="dropdown-divider" /></li>
                                                    <li>
                                                        <button
                                                            className="dropdown-item text-danger"
                                                            onClick={() => handleDeleteList(list.id, list.name)}
                                                        >
                                                            <i className="fas fa-trash me-2"></i>
                                                            Excluir
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="card-body">
                                        {list.description && (
                                            <p className="text-muted small mb-3">{list.description}</p>
                                        )}

                                        <div className="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small className="text-muted d-block">Itens</small>
                                                <strong>{list.items_count || 0}</strong>
                                            </div>
                                            <div>
                                                <small className="text-muted d-block">Comprados</small>
                                                <strong className="text-success">
                                                    {list.purchased_items_count || 0}
                                                </strong>
                                            </div>
                                            <div>
                                                <small className="text-muted d-block">Valor</small>
                                                <strong className="text-primary">
                                                    {formatCurrency(list.total_value || 0)}
                                                </strong>
                                            </div>
                                        </div>

                                        {/* Progress Bar */}
                                        <div className="mb-2">
                                            <div className="d-flex justify-content-between mb-1">
                                                <small className="text-muted">Progresso</small>
                                                <small className="text-muted">
                                                    {list.items_count > 0
                                                        ? Math.round((list.purchased_items_count / list.items_count) * 100)
                                                        : 0}%
                                                </small>
                                            </div>
                                            <div className="progress" style={{ height: '8px' }}>
                                                <div
                                                    className={`progress-bar bg-${getStatusColor(list.status)}`}
                                                    style={{
                                                        width: `${list.items_count > 0
                                                            ? (list.purchased_items_count / list.items_count) * 100
                                                            : 0}%`
                                                    }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="card-footer bg-light border-top-0">
                                        <div className="d-flex justify-content-between align-items-center">
                                            <small className="text-muted">
                                                <i className="far fa-calendar me-1"></i>
                                                {formatDate(list.created_at)}
                                            </small>
                                            <a href={`/delivery/shopping-lists/${list.id}`}
                                                className="btn btn-sm btn-primary"
                                            >
                                                Abrir
                                                <i className="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="col-12">
                            <div className="card border-0 shadow-sm">
                                <div className="card-body text-center py-5">
                                    <div className="fs-1 mb-3">📋</div>
                                    <h5 className="text-muted">Nenhuma lista de compras criada</h5>
                                    <p className="text-muted mb-3">
                                        Crie sua primeira lista para começar a organizar suas compras
                                    </p>
                                    <button
                                        className="btn btn-primary"
                                        onClick={() => setShowCreateModal(true)}
                                    >
                                        <i className="fas fa-plus me-2"></i>
                                        Criar Primeira Lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                {
                    lists?.links && (
                        <nav className="mt-4">
                            <ul className="pagination justify-content-center">
                                {lists.links.map((link, index) => (
                                    <li
                                        key={index}
                                        className={`page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`}
                                    >
                                        {link.url ? (
                                            <a
                                                href={link.url}
                                                className="page-link"
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        ) : (
                                            <span
                                                className="page-link"
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    )}
            </div>


            {
                showCreateModal && (
                    <CreateListModal
                        show={showCreateModal}
                        onClose={() => setShowCreateModal(false)}
                        onSubmit={handleCreateList}
                        data={data}
                        setData={setData}
                        errors={errors}
                    />
                )}
        </AppLayout>
    );
}


function CreateListModal({ show, onClose, onSubmit, data, setData, errors }) {
    useEffect(() => {
        if (show) {
            const modal = new window.bootstrap.Modal(document.getElementById('createListModal'));
            modal.show();

            document.getElementById('createListModal').addEventListener('hidden.bs.modal', onClose);
        }
    }, [show]);

    return (
        <div className="modal fade" id="createListModal" tabIndex="-1">
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                    <form onSubmit={onSubmit}>
                        <div className="modal-header">
                            <h5 className="modal-title">
                                <i className="fas fa-plus-circle me-2"></i>
                                Nova Lista de Compras
                            </h5>
                            <button
                                type="button"
                                className="btn-close"
                                data-bs-dismiss="modal"
                            ></button>
                        </div>

                        <div className="modal-body">
                            <div className="mb-3">
                                <label className="form-label">
                                    Nome da Lista <span className="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                    placeholder="Ex: Compras da Semana"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    autoFocus
                                />
                                {errors.name && (
                                    <div className="invalid-feedback">{errors.name}</div>
                                )}
                            </div>

                            <div className="mb-3">
                                <label className="form-label">Descrição</label>
                                <textarea
                                    className={`form-control ${errors.description ? 'is-invalid' : ''}`}
                                    rows="3"
                                    placeholder="Descreva o objetivo desta lista..."
                                    value={data.description}
                                    onChange={e => setData('description', e.target.value)}
                                />
                                {errors.description && (
                                    <div className="invalid-feedback">{errors.description}</div>
                                )}
                            </div>
                        </div>

                        <div className="modal-footer">
                            <button
                                type="button"
                                className="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button type="submit" className="btn btn-primary">
                                <i className="fas fa-check me-2"></i>
                                Criar Lista
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
