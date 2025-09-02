import './bootstrap';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import App from './components/App';

const container = document.getElementById('app');
const root = createRoot(container);

root.render(
    <Router>
        <Routes>
            <Route path="/*" element={<App />} />
        </Routes>
    </Router>
);