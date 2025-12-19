import React, { useState, useEffect } from 'react';
import api from '../../api/axios';

export default function AdminUserManagement() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filters, setFilters] = useState({
        status: '',
        profession: '',
        search: '',
    });

    useEffect(() => {
        loadUsers();
    }, [filters]);

    const loadUsers = async () => {
        try {
            const params = new URLSearchParams();
            if (filters.status) params.append('status', filters.status);
            if (filters.profession) params.append('profession', filters.profession);
            if (filters.search) params.append('search', filters.search);

            const response = await api.get(`/admin/users?${params.toString()}`);
            setUsers(response.data.data || []);
        } catch (error) {
            console.error('Error loading users:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleApprove = async (userId) => {
        try {
            await api.post(`/admin/users/${userId}/approve`);
            loadUsers();
        } catch (error) {
            console.error('Error approving user:', error);
            alert('Failed to approve user');
        }
    };

    const handleDeny = async (userId) => {
        if (!window.confirm('Are you sure you want to deny this user?')) {
            return;
        }
        try {
            await api.post(`/admin/users/${userId}/deny`);
            loadUsers();
        } catch (error) {
            console.error('Error denying user:', error);
            alert('Failed to deny user');
        }
    };

    const handleBan = async (userId) => {
        if (!window.confirm('Are you sure you want to ban this user?')) {
            return;
        }
        try {
            await api.post(`/admin/users/${userId}/ban`);
            loadUsers();
        } catch (error) {
            console.error('Error banning user:', error);
            alert('Failed to ban user');
        }
    };

    const handleUnban = async (userId) => {
        try {
            await api.post(`/admin/users/${userId}/unban`);
            loadUsers();
        } catch (error) {
            console.error('Error unbanning user:', error);
            alert('Failed to unban user');
        }
    };

    const handleAssignRole = async (userId, role) => {
        try {
            await api.post(`/admin/users/${userId}/role`, { role });
            loadUsers();
        } catch (error) {
            console.error('Error assigning role:', error);
            alert('Failed to assign role');
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
            <h1 className="text-3xl font-bold mb-6">User Management</h1>

            <div className="card bg-base-100 shadow-xl mb-6">
                <div className="card-body">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="form-control">
                            <label className="label">
                                <span className="label-text">Status</span>
                            </label>
                            <select
                                className="select select-bordered"
                                value={filters.status}
                                onChange={(e) => setFilters({ ...filters, status: e.target.value })}
                            >
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>
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
                                <span className="label-text">Search</span>
                            </label>
                            <input
                                type="text"
                                placeholder="Search users..."
                                className="input input-bordered"
                                value={filters.search}
                                onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div className="card bg-base-100 shadow-xl">
                <div className="card-body">
                    <div className="overflow-x-auto">
                        <table className="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profession</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.map((user) => (
                                    <tr key={user.id}>
                                        <td>{user.name}</td>
                                        <td>{user.email}</td>
                                        <td>{user.profession || '-'}</td>
                                        <td>
                                            {user.roles && user.roles.length > 0 ? (
                                                <select
                                                    className="select select-sm select-bordered"
                                                    value={user.roles[0]?.name || 'user'}
                                                    onChange={(e) => handleAssignRole(user.id, e.target.value)}
                                                >
                                                    <option value="user">User</option>
                                                    <option value="moderator">Moderator</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            ) : (
                                                'user'
                                            )}
                                        </td>
                                        <td>
                                            {user.is_banned ? (
                                                <span className="badge badge-error">Banned</span>
                                            ) : user.is_approved ? (
                                                <span className="badge badge-success">Approved</span>
                                            ) : (
                                                <span className="badge badge-warning">Pending</span>
                                            )}
                                        </td>
                                        <td>
                                            <div className="flex gap-2">
                                                {!user.is_approved && !user.is_banned && (
                                                    <>
                                                        <button
                                                            onClick={() => handleApprove(user.id)}
                                                            className="btn btn-xs btn-success"
                                                        >
                                                            Approve
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeny(user.id)}
                                                            className="btn btn-xs btn-error"
                                                        >
                                                            Deny
                                                        </button>
                                                    </>
                                                )}
                                                {user.is_banned ? (
                                                    <button
                                                        onClick={() => handleUnban(user.id)}
                                                        className="btn btn-xs btn-warning"
                                                    >
                                                        Unban
                                                    </button>
                                                ) : (
                                                    <button
                                                        onClick={() => handleBan(user.id)}
                                                        className="btn btn-xs btn-error"
                                                    >
                                                        Ban
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
}



