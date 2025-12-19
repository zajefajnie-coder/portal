import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/axios';

export default function PortfolioPage() {
    const { id } = useParams();
    const [portfolio, setPortfolio] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadPortfolio();
    }, [id]);

    const loadPortfolio = async () => {
        try {
            const response = await api.get(`/portfolios/${id}`);
            setPortfolio(response.data);
        } catch (error) {
            console.error('Error loading portfolio:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center min-h-screen">
                <span className="loading loading-spinner loading-lg"></span>
            </div>
        );
    }

    if (!portfolio) {
        return (
            <div className="text-center py-12">
                <p className="text-lg">Portfolio not found.</p>
            </div>
        );
    }

    return (
        <div>
            <div className="breadcrumbs mb-4">
                <ul>
                    <li><Link to="/">Home</Link></li>
                    <li><Link to="/portfolios">Portfolios</Link></li>
                    <li>{portfolio.title}</li>
                </ul>
            </div>

            <div className="card bg-base-100 shadow-xl mb-6">
                <div className="card-body">
                    <h1 className="text-3xl font-bold">{portfolio.title}</h1>
                    <p className="text-base-content/70">
                        by <Link to={`/profiles/${portfolio.user?.id}`} className="link link-primary">
                            {portfolio.user?.name}
                        </Link>
                    </p>
                    {portfolio.description && (
                        <p className="mt-4">{portfolio.description}</p>
                    )}
                    {portfolio.tags && portfolio.tags.length > 0 && (
                        <div className="flex flex-wrap gap-2 mt-4">
                            {portfolio.tags.map((tag) => (
                                <span key={tag.id} className="badge badge-primary">
                                    {tag.name}
                                </span>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            {portfolio.images && portfolio.images.length > 0 && (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {portfolio.images.map((image) => (
                        <div key={image.id} className="card bg-base-100 shadow-xl">
                            <figure>
                                <img
                                    src={`/storage/${image.image_path}`}
                                    alt={image.alt_text || portfolio.title}
                                    className="w-full h-64 object-cover"
                                />
                            </figure>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}



