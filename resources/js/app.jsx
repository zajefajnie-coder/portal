import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import Layout from './components/Layout';
import HomePage from './pages/HomePage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import ProfilePage from './pages/ProfilePage';
import PortfolioPage from './pages/PortfolioPage';
import MyPortfoliosPage from './pages/MyPortfoliosPage';
import PortfolioEditorPage from './pages/PortfolioEditorPage';
import AdminDashboard from './pages/admin/AdminDashboard';
import AdminUserManagement from './pages/admin/AdminUserManagement';
import ProtectedRoute from './components/ProtectedRoute';
import AdminRoute from './components/AdminRoute';

function App() {
    return (
        <AuthProvider>
            <Layout>
                <Routes>
                    <Route path="/" element={<HomePage />} />
                    <Route path="/login" element={<LoginPage />} />
                    <Route path="/register" element={<RegisterPage />} />
                    <Route path="/profiles/:id" element={<ProfilePage />} />
                    <Route path="/portfolios/:id" element={<PortfolioPage />} />
                    
                    <Route element={<ProtectedRoute />}>
                        <Route path="/my-portfolios" element={<MyPortfoliosPage />} />
                        <Route path="/portfolios/new" element={<PortfolioEditorPage />} />
                        <Route path="/portfolios/:id/edit" element={<PortfolioEditorPage />} />
                    </Route>
                    
                    <Route element={<AdminRoute />}>
                        <Route path="/admin" element={<AdminDashboard />} />
                        <Route path="/admin/users" element={<AdminUserManagement />} />
                    </Route>
                </Routes>
            </Layout>
        </AuthProvider>
    );
}

export default App;


