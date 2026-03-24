import { Link, usePage } from '@inertiajs/react';

export default function AppLayout({ children }) {
    const { auth } = usePage().props;
    const currentUrl = usePage().url;

    const isActive = (path) => currentUrl === path || currentUrl.startsWith(path + '/');

    return (
        <div className="container-fluid">
            <div className="row">
                <nav id="sidebar" className="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" style={{ minHeight: '100vh' }}>
                    <div className="pt-3">
                        <div className="text-center mb-4">
                            <img src="/images/logo.png" alt="Logo" width="200" />
                            <h5 className="mt-2">Rock and Burger</h5>
                        </div>
                        <ul className="nav flex-column">
                            <li className="nav-item">
                                <a className={`nav-link ${isActive('/delivery/home') ? 'active' : ''}`} href="/delivery/home">
                                    <i className="bi bi-house-door me-2"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className={`nav-link ${isActive('/delivery/orders') ? 'active' : ''}`} href="/delivery/orders">
                                    <i className="bi bi-cart me-2"></i>
                                    Pedidos
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className={`nav-link ${isActive('/delivery/products') ? 'active' : ''}`} href="/delivery/products">
                                    <i className="bi bi-box me-2"></i>
                                    Produtos
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className={`nav-link ${isActive('/delivery/customers') ? 'active' : ''}`} href="/delivery/customers">
                                    <i className="bi bi-people me-2"></i>
                                    Clientes
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className={`nav-link ${isActive('/delivery/expenses') ? 'active' : ''}`} href="/delivery/expenses">
                                    <i className="bi bi-cash-coin me-2"></i>
                                    Despesas
                                </a>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/delivery/shopping-lists') ? 'active' : ''}`} href="/delivery/shopping-lists">
                                    <i className="fa-solid fa-basket-shopping me-2"></i>
                                    Lista de compras
                                </Link>
                            </li>
                            {auth?.user?.role === 'admin' && (
                                <li className="nav-item">
                                    <Link className={`nav-link ${isActive('/admin/users') ? 'active' : ''}`} href="/admin/users">
                                        <i className="bi bi-person me-2"></i>
                                        Usuários
                                    </Link>
                                </li>
                            )}
                        </ul>
                        <a className="nav-link d-sm-none bg-secondary-subtle text-dark mt-3" style={{ cursor: 'pointer' }} data-bs-toggle="collapse" data-bs-target="#sidebar">
                            <i className="bi bi-list"></i> Fechar Menu
                        </a>
                    </div>
                </nav>

                <main className="col-md-9 ms-sm-auto col-lg-10 p-0">
                    <nav className="navbar navbar-expand-lg navbar-light bg-white border-bottom" id="main_navbar">
                        <div className="container-fluid">
                            <button className="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                                <span className="navbar-toggler-icon"></span>
                            </button>
                            <div className="ms-auto">
                                {auth?.user ? (
                                    <div className="dropdown">
                                        <button className="btn bg-secondary-subtle text-dark me-2 dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i className="fa-solid fa-user"></i>&nbsp;{auth.user.name}
                                        </button>
                                        <ul className="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                            <li>
                                                <a className="dropdown-item" href="/profile">
                                                    <i className="fa-solid fa-id-badge"></i>&nbsp;Perfil
                                                </a>
                                            </li>
                                            <li>
                                                <Link href="/logout" method="post" as="button" className="dropdown-item">
                                                    <i className="fa-solid fa-person-running"></i>&nbsp;Sair
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                ) : (
                                    <div>
                                        <a href="/login" className="btn btn-light text-dark me-2">Entrar</a>
                                        <a href="/register" className="btn btn-primary">Registrar-se</a>
                                    </div>
                                )}
                            </div>
                        </div>
                    </nav>

                    <div id="page-content" className="p-3">
                        {children}
                    </div>
                </main>
            </div>
            <div className="spinner-border main-loading d-none" role="status">
                <span className="visually-hidden">Loading...</span>
            </div>
            <div className="modal fade" tabIndex="-1" id="modalMessage">
                <div className="modal-dialog d-flex align-items-center justify-content-center" style={{ minHeight: '100vh' }}>
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="modalMessageHeaderTitle"></h5>
                            <button type="button" className="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div className="modal-body" id="modalMessageBody"></div>
                    </div>
                </div>
            </div>
        </div>
    );
}
