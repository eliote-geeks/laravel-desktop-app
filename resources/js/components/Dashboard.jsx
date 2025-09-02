import { useState, useEffect } from 'react';
import { Card, Row, Col, Alert } from 'react-bootstrap';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';

export default function Dashboard() {
    const { user } = useAuth();
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            const response = await axios.get('/api/v1/dashboard');
            setStats(response.data.data.stats);
        } catch (err) {
            setError('Erreur lors du chargement des données');
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="d-flex justify-content-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return (
        <div>
            <h1 className="mb-4">Bienvenue, {user?.name}</h1>
            
            {error && <Alert variant="danger">{error}</Alert>}
            
            <Row>
                <Col md={6} lg={4} className="mb-3">
                    <Card>
                        <Card.Body>
                            <Card.Title>Utilisateurs totaux</Card.Title>
                            <Card.Text className="fs-3 text-primary">
                                {stats?.total_users || 0}
                            </Card.Text>
                        </Card.Body>
                    </Card>
                </Col>
                
                <Col md={6} lg={4} className="mb-3">
                    <Card>
                        <Card.Body>
                            <Card.Title>Statut Redis</Card.Title>
                            <Card.Text className="fs-6">
                                {stats?.redis_status ? 
                                    <span className="badge bg-success">Connecté</span> : 
                                    <span className="badge bg-danger">Déconnecté</span>
                                }
                            </Card.Text>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </div>
    );
}