import { Head, router, usePage } from '@inertiajs/react';
import AppLayout from '../../../Layouts/AppLayout';
import { useState } from 'react';

export default function AdminUsersIndex() {
    const { pendingUsers, approvedUsers } = usePage().props;
    const [loading, setLoading] = useState(false);

    // Helpers to show loading/alerts if available globally
    const handleAction = (url, method = 'post', confirmMessage = null) => {
        if (confirmMessage && !window.confirm(confirmMessage)) {
            return;
        }

        setLoading(true);
        if (window.showLoading) window.showLoading(true);

        router[method](url, {}, {
            onFinish: () => {
                setLoading(false);
                if (window.showLoading) window.showLoading(false);
            },
            preserveScroll: true
        });
    };

    return (
        <AppLayout>
            <Head title="Gestão de Usuários" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <h2 className="mb-0">Gestão de Usuários</h2>
                </div>

                {/* Usuários Pendentes */}
                <div className="card shadow-sm border-0 mb-4">
                    <div className="card-header bg-white border-bottom pb-0 pt-4">
                        <h5 className="text-warning">
                            <i className="bi bi-person-lines-fill me-2"></i>
                            Pendentes de Aprovação ({pendingUsers.length})
                        </h5>
                    </div>
                    <div className="card-body p-0">
                        {pendingUsers.length > 0 ? (
                            <div className="table table-responsive">
                                <table className="table table-hover align-middle mb-0">
                                    <thead className="table-light">
                                        <tr>
                                            <th className="px-4">Nome</th>
                                            <th className="d-none d-sm-table-cell">Email</th>
                                            <th className="d-none d-sm-table-cell">Data de Registro</th>
                                            <th className="px-4">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {pendingUsers.map(user => (
                                            <tr key={user.id}>
                                                <td className="px-4 fw-medium">{user.name}</td>
                                                <td className="text-muted d-none d-sm-table-cell">{user.email}</td>
                                                <td className="text-muted d-none d-sm-table-cell">
                                                    {user.created_at ? new Date(user.created_at).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short'}) : 'N/A'}
                                                </td>
                                                <td className="px-4">
                                                    <button
                                                        onClick={() => handleAction(`/admin/users/${user.id}/approve`, 'post')}
                                                        className="btn btn-sm btn-success me-2 my-1 w-100"
                                                        disabled={loading}
                                                    >
                                                        <i className="bi bi-check-lg me-1"></i> Aprovar
                                                    </button>
                                                    <button
                                                        onClick={() => handleAction(`/admin/users/${user.id}/reject`, 'delete', 'Tem certeza que deseja rejeitar este usuário?')}
                                                        className="btn btn-sm btn-outline-danger my-1 w-100"
                                                        disabled={loading}
                                                    >
                                                        <i className="bi bi-x-lg me-1"></i> Rejeitar
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="p-5 text-center text-muted">
                                <p className="mb-0">Nenhum usuário pendente de aprovação.</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Usuários Aprovados */}
                <div className="card shadow-sm border-0 mb-4">
                    <div className="card-header bg-white border-bottom pb-0 pt-4">
                        <h5 className="text-success">
                            <i className="bi bi-person-check-fill me-2"></i>
                            Usuários Aprovados ({approvedUsers.length})
                        </h5>
                    </div>
                    <div className="card-body p-0">
                        {approvedUsers.length > 0 ? (
                            <div className="table-responsive">
                                <table className="table table-hover align-middle mb-0">
                                    <thead className="table-light">
                                        <tr>
                                            <th className="px-4">Nome</th>
                                            <th className="d-none d-sm-table-cell">Email</th>
                                            <th>Aprovado em</th>
                                            <th className="px-4">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {approvedUsers.map(user => (
                                            <tr key={user.id}>
                                                <td className="px-4 fw-medium">{user.name}</td>
                                                <td className="text-muted d-none d-sm-table-cell">{user.email}</td>
                                                <td className="text-muted">
                                                    {user.approved_at ? new Date(user.approved_at).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short'}) : 'N/A'}
                                                </td>
                                                <td className="px-4">
                                                    <button
                                                        onClick={() => handleAction(`/admin/users/${user.id}/revoke`, 'patch', 'Tem certeza que deseja revogar o acesso deste usuário?')}
                                                        className="btn btn-sm btn-outline-warning"
                                                        disabled={loading}
                                                    >
                                                        Revogar Acesso
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="p-5 text-center text-muted">
                                <p className="mb-0">Nenhum usuário aprovado ainda.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
