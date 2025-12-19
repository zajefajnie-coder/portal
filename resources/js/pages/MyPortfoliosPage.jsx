import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api/axios';

export default function MyPortfoliosPage() {
    const [portfolios, setPortfolios] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadPortfolios();
    }, []);

    const loadPortfolios = async () => {
        try {
            const response = await api.get('/my-portfolios');
            setPortfolios(response.data);
        } catch (error) {
            console.error('Error loading portfolios:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('Are you sure you want to delete this portfolio?')) {
            return;
        }

        try {
            await api.delete(`/portfolios/${id}`);
            loadPortfolios();
        } catch (error) {
            console.error('Error deleting portfolio:', error);
            alert('Failed to delete portfolio');
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center min-h-screen">
                <span className="loading loading-spinner loading-lg"></span>
            </div>
        );
    }

    return (
        <div>
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-3xl font-bold">My Portfolios</h1>
                <Link to="/portfolios/new" className="btn btn-primary">
                    Create New Portfolio
                </Link>
            </div>

            {portfolios.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {portfolios.map((portfolio) => (
                        <div key={portfolio.id} className="card bg-base-100 shadow-xl">
                            {portfolio.images && portfolio.images.length > 0 && (
                                <figure>
                                    <img
                                        src={`/storage/${portfolio.images[0].image_path}`}
                                        alt={portfolio.title}
                                        className="w-full h-64 object-cover"
                                    />
                                </figure>
                            )}
                            <div className="card-body">
                                <h2 className="card-title">{portfolio.title}</h2>
                                <p className="text-sm">
                                    {portfolio.is_public ? (
                                        <span className="badge badge-success">Public</span>
                                    ) : (
                                        <span className="badge badge-warning">Private</span>
                                    )}
                                </p>
                                <div className="card-actions justify-end mt-4">
                                    <Link
                                        to={`/portfolios/${portfolio.id}/edit`}
                                        className="btn btn-sm btn-primary"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        onClick={() => handleDelete(portfolio.id)}
                                        className="btn btn-sm btn-error"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <div className="text-center py-12">
                    <p className="text-lg mb-4">You don't have any portfolios yet.</p>
                    <Link to="/portfolios/new" className="btn btn-primary">
                        Create Your First Portfolio
                    </Link>
                </div>
            )}
        </div>
    );
}



