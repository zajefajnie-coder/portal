import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api/axios';

export default function AdminDashboard() {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadStats();
    }, []);

    const loadStats = async () => {
        try {
            const response = await api.get('/admin/dashboard');
            setStats(response.data);
        } catch (error) {
            console.error('Error loading stats:', error);
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

    if (!stats) {
        return <div>Error loading dashboard</div>;
    }

    return (
        <div>
            <h1 className="text-3xl font-bold mb-6">Admin Dashboard</h1>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div className="stat bg-base-100 shadow rounded-lg">
                    <div className="stat-title">Total Users</div>
                    <div className="stat-value">{stats.total_users}</div>
                </div>
                <div className="stat bg-base-100 shadow rounded-lg">
                    <div className="stat-title">Pending Approvals</div>
                    <div className="stat-value text-warning">{stats.pending_approvals}</div>
                </div>
                <div className="stat bg-base-100 shadow rounded-lg">
                    <div className="stat-title">Banned Users</div>
                    <div className="stat-value text-error">{stats.banned_users}</div>
                </div>
                <div className="stat bg-base-100 shadow rounded-lg">
                    <div className="stat-title">Total Portfolios</div>
                    <div className="stat-value">{stats.total_portfolios}</div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="card bg-base-100 shadow-xl">
                    <div className="card-body">
                        <h2 className="card-title">Recent Registrations</h2>
                        <div className="overflow-x-auto">
                            <table className="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Profession</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stats.recent_registrations?.map((user) => (
                                        <tr key={user.id}>
                                            <td>{user.name}</td>
                                            <td>{user.email}</td>
                                            <td>{user.profession || '-'}</td>
                                            <td>
                                                {user.is_approved ? (
                                                    <span className="badge badge-success">Approved</span>
                                                ) : (
                                                    <span className="badge badge-warning">Pending</span>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="card-actions justify-end">
                            <Link to="/admin/users" className="btn btn-sm btn-primary">
                                View All Users
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="card bg-base-100 shadow-xl">
                    <div className="card-body">
                        <h2 className="card-title">Top Profiles</h2>
                        <div className="overflow-x-auto">
                            <table className="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Profession</th>
                                        <th>City</th>
                                        <th>Portfolios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stats.top_profiles?.map((profile) => (
                                        <tr key={profile.id}>
                                            <td>{profile.name}</td>
                                            <td>{profile.profession || '-'}</td>
                                            <td>{profile.city || '-'}</td>
                                            <td>{profile.portfolios_count}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {stats.reported_images > 0 && (
                <div className="alert alert-warning mt-6">
                    <span>You have {stats.reported_images} reported images that need moderation.</span>
                </div>
            )}
        </div>
    );
}



