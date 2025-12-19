import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function Layout({ children }) {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate('/');
    };

    return (
        <div className="min-h-screen bg-base-200">
            <div className="navbar bg-base-100 shadow-lg">
                <div className="navbar-start">
                    <Link to="/" className="btn btn-ghost text-xl">
                        Laravel Portal Modelingowy
                    </Link>
                </div>
                <div className="navbar-center hidden lg:flex">
                    <ul className="menu menu-horizontal px-1">
                        <li><Link to="/">Home</Link></li>
                        <li><Link to="/profiles">Profiles</Link></li>
                        <li><Link to="/portfolios">Portfolios</Link></li>
                    </ul>
                </div>
                <div className="navbar-end">
                    {user ? (
                        <div className="dropdown dropdown-end">
                            <label tabIndex={0} className="btn btn-ghost btn-circle avatar">
                                <div className="w-10 rounded-full">
                                    {user.avatar ? (
                                        <img src={user.avatar} alt={user.name} />
                                    ) : (
                                        <div className="bg-primary text-primary-content flex items-center justify-center">
                                            {user.name.charAt(0).toUpperCase()}
                                        </div>
                                    )}
                                </div>
                            </label>
                            <ul tabIndex={0} className="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                                <li><Link to="/my-portfolios">My Portfolios</Link></li>
                                <li><Link to="/portfolios/new">New Portfolio</Link></li>
                                {user.roles?.some(r => r.name === 'admin' || r.name === 'moderator') && (
                                    <li><Link to="/admin">Admin Panel</Link></li>
                                )}
                                <li><button onClick={handleLogout}>Logout</button></li>
                            </ul>
                        </div>
                    ) : (
                        <div className="flex gap-2">
                            <Link to="/login" className="btn btn-ghost">Login</Link>
                            <Link to="/register" className="btn btn-primary">Register</Link>
                        </div>
                    )}
                </div>
            </div>
            <main className="container mx-auto px-4 py-8">
                {children}
            </main>
            <footer className="footer footer-center p-10 bg-base-200 text-base-content">
                <div>
                    <p className="font-bold">Laravel Portal Modelingowy</p>
                    <p>Professional modeling & creative industry portal</p>
                </div>
            </footer>
        </div>
    );
}



