import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api/axios';

export default function HomePage() {
    const [portfolios, setPortfolios] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filters, setFilters] = useState({
        profession: '',
        city: '',
        search: '',
    });

    useEffect(() => {
        loadPortfolios();
    }, [filters]);

    const loadPortfolios = async () => {
        try {
            const params = new URLSearchParams();
            if (filters.profession) params.append('profession', filters.profession);
            if (filters.city) params.append('city', filters.city);
            if (filters.search) params.append('search', filters.search);

            const response = await api.get(`/portfolios?${params.toString()}`);
            setPortfolios(response.data.data || []);
        } catch (error) {
            console.error('Error loading portfolios:', error);
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

    return (
        <div>
            <div className="hero bg-base-200 rounded-lg mb-8">
                <div className="hero-content text-center">
                    <div className="max-w-md">
                        <h1 className="text-5xl font-bold">Welcome to Laravel Portal Modelingowy</h1>
                        <p className="py-6">Discover talented models, photographers, makeup artists, hairstylists, and fashion stylists.</p>
                    </div>
                </div>
            </div>

            <div className="card bg-base-100 shadow-xl mb-8">
                <div className="card-body">
                    <h2 className="card-title mb-4">Search & Filter</h2>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="form-control">
                            <label className="label">
                                <span className="label-text">Profession</span>
                            </label>
                            <select
                                className="select select-bordered"
                                value={filters.profession}
                                onChange={(e) => setFilters({ ...filters, profession: e.target.value })}
                            >
                                <option value="">All</option>
                                <option value="model">Model</option>
                                <option value="photographer">Photographer</option>
                                <option value="makeup_artist">Makeup Artist</option>
                                <option value="hairstylist">Hairstylist</option>
                                <option value="fashion_stylist">Fashion Stylist</option>
                            </select>
                        </div>
                        <div className="form-control">
                            <label className="label">
                                <span className="label-text">City</span>
                            </label>
                            <input
                                type="text"
                                placeholder="Enter city"
                                className="input input-bordered"
                                value={filters.city}
                                onChange={(e) => setFilters({ ...filters, city: e.target.value })}
                            />
                        </div>
                        <div className="form-control">
                            <label className="label">
                                <span className="label-text">Search</span>
                            </label>
                            <input
                                type="text"
                                placeholder="Search portfolios..."
                                className="input input-bordered"
                                value={filters.search}
                                onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                            />
                        </div>
                    </div>
                </div>
            </div>

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
                            <p className="text-sm text-base-content/70">
                                by {portfolio.user?.name}
                            </p>
                            {portfolio.tags && portfolio.tags.length > 0 && (
                                <div className="flex flex-wrap gap-2">
                                    {portfolio.tags.map((tag) => (
                                        <span key={tag.id} className="badge badge-primary">
                                            {tag.name}
                                        </span>
                                    ))}
                                </div>
                            )}
                            <div className="card-actions justify-end">
                                <Link to={`/portfolios/${portfolio.id}`} className="btn btn-primary btn-sm">
                                    View
                                </Link>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {portfolios.length === 0 && (
                <div className="text-center py-12">
                    <p className="text-lg">No portfolios found. Be the first to create one!</p>
                </div>
            )}
        </div>
    );
}



