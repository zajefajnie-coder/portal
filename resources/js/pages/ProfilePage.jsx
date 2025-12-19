import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/axios';

export default function ProfilePage() {
    const { id } = useParams();
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadProfile();
    }, [id]);

    const loadProfile = async () => {
        try {
            const response = await api.get(`/profiles/${id}`);
            setUser(response.data);
        } catch (error) {
            console.error('Error loading profile:', error);
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

    if (!user) {
        return (
            <div className="text-center py-12">
                <p className="text-lg">Profile not found.</p>
            </div>
        );
    }

    return (
        <div>
            <div className="card bg-base-100 shadow-xl mb-6">
                <div className="card-body">
                    <div className="flex items-center gap-4">
                        {user.avatar ? (
                            <img src={user.avatar} alt={user.name} className="w-24 h-24 rounded-full" />
                        ) : (
                            <div className="w-24 h-24 rounded-full bg-primary text-primary-content flex items-center justify-center text-3xl">
                                {user.name.charAt(0).toUpperCase()}
                            </div>
                        )}
                        <div>
                            <h1 className="text-3xl font-bold">{user.name}</h1>
                            {user.profession && (
                                <p className="badge badge-primary mt-2">
                                    {user.profession.replace('_', ' ')}
                                </p>
                            )}
                            {user.city && <p className="text-base-content/70 mt-1">{user.city}</p>}
                        </div>
                    </div>
                    {user.bio && (
                        <p className="mt-4">{user.bio}</p>
                    )}
                    {user.social_links && (
                        <div className="flex gap-2 mt-4">
                            {user.social_links.instagram && (
                                <a href={user.social_links.instagram} target="_blank" rel="noopener noreferrer" className="btn btn-sm btn-ghost">
                                    Instagram
                                </a>
                            )}
                            {user.social_links.website && (
                                <a href={user.social_links.website} target="_blank" rel="noopener noreferrer" className="btn btn-sm btn-ghost">
                                    Website
                                </a>
                            )}
                        </div>
                    )}
                </div>
            </div>

            <h2 className="text-2xl font-bold mb-4">Portfolios</h2>
            {user.public_portfolios && user.public_portfolios.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {user.public_portfolios.map((portfolio) => (
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
                                <h3 className="card-title">{portfolio.title}</h3>
                                <div className="card-actions justify-end">
                                    <Link to={`/portfolios/${portfolio.id}`} className="btn btn-primary btn-sm">
                                        View
                                    </Link>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-center py-12">No portfolios yet.</p>
            )}
        </div>
    );
}



