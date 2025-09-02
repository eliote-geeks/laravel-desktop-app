import { useState, useEffect } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { Container } from 'react-bootstrap';
import Navigation from './Navigation';
import Dashboard from './Dashboard';
import Login from './auth/Login';
import Register from './auth/Register';
import { AuthProvider, useAuth } from '../contexts/AuthContext';

function AppContent() {
    const { user, loading } = useAuth();

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center vh-100">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return (
        <div className="App">
            {user && <Navigation />}
            <Container fluid={!user} className={user ? "mt-3" : ""}>
                <Routes>
                    <Route path="/login" element={user ? <Navigate to="/dashboard" /> : <Login />} />
                    <Route path="/register" element={user ? <Navigate to="/dashboard" /> : <Register />} />
                    <Route path="/dashboard" element={user ? <Dashboard /> : <Navigate to="/login" />} />
                    <Route path="/" element={<Navigate to={user ? "/dashboard" : "/login"} />} />
                </Routes>
            </Container>
        </div>
    );
}

export default function App() {
    return (
        <AuthProvider>
            <AppContent />
        </AuthProvider>
    );
}