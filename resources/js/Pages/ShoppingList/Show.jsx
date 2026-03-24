import { Head, router, useForm, usePage } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import { useState, useEffect } from 'react';
import { formatCurrency } from '../../Utils/legacyHelpers';

export default function ShoppingListShow() {
    const { list } = usePage().props;
    const [showAddItemModal, setShowAddItemModal] = useState(false);
    const [editingItem, setEditingItem] = useState(null);

    const { data, setData, post, reset, errors } = useForm({
        product_name: '',
        quantity: 1,
        unit: 'un',
        estimated_price: '',
        notes: '',
    });

    const handleAddItem = (e) => {
        e.preventDefault();
        showLoading(true);

        post(`/delivery/shopping-lists/${list.id}/items`, {
            onSuccess: () => {
                reset();
                setShowAddItemModal(false);
                modalMessage({
                    title: 'Sucesso!',
                    description: 'Item adicionado à lista!',
                    type: 'success',
                    time: 2000
                });
            },
            onFinish: () => showLoading(false)
        });
    };

    const toggleItemPurchased = (itemId, currentStatus) => {
        showLoading(true);
        router.patch(`/delivery/shopping-lists/${list.id}/items/${itemId}/toggle`, {
            purchased: !currentStatus
        }, {
            onSuccess: () => {
                modalMessage({
                    title: 'Atualizado!',
                    description: !currentStatus ? 'Item marcado como comprado!' : 'Item desmarcado',
                    type: 'success',
                    time: 1500
                });
            },
            onFinish: () => showLoading(false)
        });
    };

    const deleteItem = (itemId, itemName) => {
        modalMessage({
            title: 'Confirmar Exclusão',
            description: `Deseja remover "${itemName}" da lista?`,
            type: 'warning',
            buttonText: 'Sim, remover',
            buttonId: 'btn-delete-item',
            callback: () => {
                document.getElementById('btn-delete-item')?.addEventListener('click', () => {
                    showLoading(true);
                    router.delete(`/delivery/shopping-lists/${list.id}/items/${itemId}`, {
                        onSuccess: () => {
                            modalMessage({
                                title: 'Removido',
                                description: 'Item removido da lista!',
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

    const updateListStatus = (status) => {
        showLoading(true);
        router.patch(`/delivery/shopping-lists/${list.id}/status`, { status }, {
            onSuccess: () => {
                modalMessage({
                    title: 'Status Atualizado!',
                    description: 'O status da lista foi alterado com sucesso.',
                    type: 'success',
                    time: 2000
                });
            },
            onFinish: () => showLoading(false)
        });
    };

    const progress = list.items?.length > 0
        ? Math.round((list.purchased_items_count / list.items.length) * 100)
        : 0;

    return (
        <AppLayout>
            <Head title={`Lista: ${list.name}`} />

            <div className="container-fluid">
                {/* Header */}
                <div className="row mb-4">
                    <div className="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol className="breadcrumb">
                                <li className="breadcrumb-item">
                                    <a href="/delivery/shopping-lists">Listas de Compras</a>
                                </li>
                                <li className="breadcrumb-item active">{list.name}</li>
                            </ol>
                        </nav>
                        <h2 className="mb-1">🛒 {list.name}</h2>
                        {list.description && (
                            <p className="text-muted mb-0">{list.description}</p>
                        )}
                    </div>
                    <div className="col-md-4 text-end">
                        <button
                            className="btn btn-primary me-2"
                            onClick={() => setShowAddItemModal(true)}
                        >
                            <i className="fas fa-plus me-2"></i>
                            Adicionar Item
                        </button>
                        <div className="btn-group">
                            <button
                                type="button"
                                className="btn btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown"
                            >
                                <i className="fas fa-cog"></i>
                            </button>
                            <ul className="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a className="dropdown-item" href={`/shopping-lists/${list.id}/edit`}>
                                        <i className="fas fa-edit me-2"></i>
                                        Editar Lista
                                    </a>
                                </li>
                                <li><hr className="dropdown-divider" /></li>
                                <li>
                                    <h6 className="dropdown-header">Alterar Status</h6>
                                </li>
                                <li>
                                    <button
                                        className="dropdown-item"
                                        onClick={() => updateListStatus('pending')}
                                    >
                                        <i className="fas fa-clock text-warning me-2"></i>
                                        Pendente
                                    </button>
                                </li>
                                <li>
                                    <button
                                        className="dropdown-item"
                                        onClick={() => updateListStatus('in_progress')}
                                    >
                                        <i className="fas fa-shopping-cart text-info me-2"></i>
                                        Em Andamento
                                    </button>
                                </li>
                                <li>
                                    <button
                                        className="dropdown-item"
                                        onClick={() => updateListStatus('completed')}
                                    >
                                        <i className="fas fa-check-circle text-success me-2"></i>
                                        Concluída
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {/* Stats */}
                <div className="row g-3 mb-4">
                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm">
                            <div className="card-body">
                                <h6 className="text-muted mb-2">Progresso Geral</h6>
                                <h3 className="mb-2">{progress}%</h3>
                                <div className="progress" style={{ height: '10px' }}>
                                    <div
                                        className="progress-bar bg-success"
                                        style={{ width: `${progress}%` }}
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm">
                            <div className="card-body">
                                <h6 className="text-muted mb-2">Total de Itens</h6>
                                <h3 className="mb-0">{list.items?.length || 0}</h3>
                                <small className="text-muted">
                                    {list.purchased_items_count || 0} comprados
                                </small>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm">
                            <div className="card-body">
                                <h6 className="text-muted mb-2">Valor Estimado</h6>
                                <h3 className="mb-0 text-primary">
                                    {formatCurrency(list.total_value || 0)}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="card border-0 shadow-sm">
                            <div className="card-body">
                                <h6 className="text-muted mb-2">Status</h6>
                                <h5 className="mb-0">
                                    <StatusBadge status={list.status} />
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Items List */}
                <div className="row">
                    <div className="col-12">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 className="mb-0">Itens da Lista</h5>
                                <div className="d-flex gap-2">
                                    <button className="btn btn-sm btn-outline-secondary">
                                        <i className="fas fa-filter me-1"></i>
                                        Filtrar
                                    </button>
                                    <button className="btn btn-sm btn-outline-secondary">
                                        <i className="fas fa-download me-1"></i>
                                        Exportar
                                    </button>
                                </div>
                            </div>

                            <div className="card-body p-0">
                                {list.items && list.items.length > 0 ? (
                                    <div className="list-group list-group-flush">
                                        {list.items.map((item, index) => (
                                            <div
                                                key={item.id}
                                                className={`list-group-item ${item.purchased ? 'bg-light' : ''}`}
                                            >
                                                <div className="d-flex align-items-center">
                                                    {/* Checkbox */}
                                                    <div className="form-check me-3">
                                                        <input
                                                            className="form-check-input"
                                                            type="checkbox"
                                                            checked={item.purchased || false}
                                                            onChange={() => toggleItemPurchased(item.id, item.purchased)}
                                                            style={{ cursor: 'pointer' }}
                                                        />
                                                    </div>

                                                    {/* Item Number */}
                                                    <div className="me-3">
                                                        <span className="badge bg-secondary">{index + 1}</span>
                                                    </div>

                                                    {/* Item Info */}
                                                    <div className="flex-grow-1">
                                                        <h6 className={`mb-1 ${item.purchased ? 'text-decoration-line-through text-muted' : ''}`}>
                                                            {item.product_name}
                                                        </h6>
                                                        <div className="d-flex gap-3">
                                                            <small className="text-muted">
                                                                <i className="fas fa-box me-1"></i>
                                                                {item.quantity} {item.unit}
                                                            </small>
                                                            {item.estimated_price && (
                                                                <small className="text-muted">
                                                                    <i className="fas fa-tag me-1"></i>
                                                                    {formatCurrency(item.estimated_price)}
                                                                </small>
                                                            )}
                                                            {item.actual_price && (
                                                                <small className="text-success">
                                                                    <i className="fas fa-receipt me-1"></i>
                                                                    Real: {formatCurrency(item.actual_price)}
                                                                </small>
                                                            )}
                                                        </div>
                                                        {item.notes && (
                                                            <small className="text-muted d-block mt-1">
                                                                <i className="fas fa-sticky-note me-1"></i>
                                                                {item.notes}
                                                            </small>
                                                        )}
                                                    </div>

                                                    {/* Actions */}
                                                    <div className="d-flex gap-2">
                                                        {item.purchased && (
                                                            <span className="badge bg-success">
                                                                <i className="fas fa-check me-1"></i>
                                                                Comprado
                                                            </span>
                                                        )}
                                                        <button
                                                            className="btn btn-sm btn-outline-danger"
                                                            onClick={() => deleteItem(item.id, item.product_name)}
                                                            title="Remover item"
                                                        >
                                                            <i className="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-5">
                                        <div className="fs-1 mb-3">📝</div>
                                        <h5 className="text-muted">Nenhum item adicionado</h5>
                                        <p className="text-muted mb-3">
                                            Comece adicionando itens à sua lista de compras
                                        </p>
                                        <button
                                            className="btn btn-primary"
                                            onClick={() => setShowAddItemModal(true)}
                                        >
                                            <i className="fas fa-plus me-2"></i>
                                            Adicionar Primeiro Item
                                        </button>
                                    </div>
                                )}
                            </div>

                            {list.items && list.items.length > 0 && (
                                <div className="card-footer bg-light">
                                    <div className="row text-center">
                                        <div className="col-md-4">
                                            <strong>Total de Itens:</strong> {list.items.length}
                                        </div>
                                        <div className="col-md-4">
                                            <strong>Comprados:</strong> {list.purchased_items_count || 0}
                                        </div>
                                        <div className="col-md-4">
                                            <strong>Valor Total:</strong> {formatCurrency(list.total_value || 0)}
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
            {showAddItemModal && (
                <AddItemModal
                    show={showAddItemModal}
                    onClose={() => setShowAddItemModal(false)}
                    onSubmit={handleAddItem}
                    data={data}
                    setData={setData}
                    errors={errors}
                />
            )}
        </AppLayout>
    );
}

function StatusBadge({ status }) {
    const statusConfig = {
        pending: { color: 'warning', label: 'Pendente', icon: 'clock' },
        in_progress: { color: 'info', label: 'Em Andamento', icon: 'shopping-cart' },
        completed: { color: 'success', label: 'Concluída', icon: 'check-circle' },
        cancelled: { color: 'danger', label: 'Cancelada', icon: 'times-circle' }
    };

    const config = statusConfig[status] || statusConfig.pending;

    return (
        <span className={`badge bg-${config.color}`}>
            <i className={`fas fa-${config.icon} me-1`}></i>
            {config.label}
        </span>
    );
}

function AddItemModal({ show, onClose, onSubmit, data, setData, errors }) {
    useEffect(() => {
        if (show) {
            const modal = new window.bootstrap.Modal(document.getElementById('addItemModal'));
            modal.show();
            document.getElementById('addItemModal').addEventListener('hidden.bs.modal', onClose);
        }
    }, [show]);

    return (
        <div className="modal fade" id="addItemModal" tabIndex="-1">
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                    <form onSubmit={onSubmit}>
                        <div className="modal-header">
                            <h5 className="modal-title">
                                <i className="fas fa-plus-circle me-2"></i>
                                Adicionar Item
                            </h5>
                            <button type="button" className="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div className="modal-body">
                            <div className="mb-3">
                                <label className="form-label">
                                    Nome do Produto <span className="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    className={`form-control ${errors.product_name ? 'is-invalid' : ''}`}
                                    placeholder="Ex: Farinha de Trigo"
                                    value={data.product_name}
                                    onChange={e => setData('product_name', e.target.value)}
                                    autoFocus
                                />
                                {errors.product_name && (
                                    <div className="invalid-feedback">{errors.product_name}</div>
                                )}
                            </div>

                            <div className="row">
                                <div className="col-md-6 mb-3">
                                    <label className="form-label">
                                        Quantidade <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        className={`form-control ${errors.quantity ? 'is-invalid' : ''}`}
                                        value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)}
                                        min="1"
                                        step="0.01"
                                    />
                                    {errors.quantity && (
                                        <div className="invalid-feedback">{errors.quantity}</div>
                                    )}
                                </div>

                                <div className="col-md-6 mb-3">
                                    <label className="form-label">Unidade</label>
                                    <select
                                        className="form-select"
                                        value={data.unit}
                                        onChange={e => setData('unit', e.target.value)}
                                    >
                                        <option value="un">Unidade</option>
                                        <option value="kg">Quilograma</option>
                                        <option value="g">Grama</option>
                                        <option value="l">Litro</option>
                                        <option value="ml">Mililitro</option>
                                        <option value="cx">Caixa</option>
                                        <option value="pct">Pacote</option>
                                    </select>
                                </div>
                            </div>

                            <div className="mb-3">
                                <label className="form-label">Preço Estimado</label>
                                <div className="input-group">
                                    <span className="input-group-text">R$</span>
                                    <input
                                        type="number"
                                        className={`form-control ${errors.estimated_price ? 'is-invalid' : ''}`}
                                        placeholder="0,00"
                                        value={data.estimated_price}
                                        onChange={e => setData('estimated_price', e.target.value)}
                                        step="0.01"
                                    />
                                    {errors.estimated_price && (
                                        <div className="invalid-feedback">{errors.estimated_price}</div>
                                    )}
                                </div>
                            </div>

                            <div className="mb-3">
                                <label className="form-label">Observações</label>
                                <textarea
                                    className="form-control"
                                    rows="2"
                                    placeholder="Ex: Marca específica, preferências..."
                                    value={data.notes}
                                    onChange={e => setData('notes', e.target.value)}
                                />
                            </div>
                        </div>

                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" className="btn btn-primary">
                                <i className="fas fa-check me-2"></i>
                                Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
