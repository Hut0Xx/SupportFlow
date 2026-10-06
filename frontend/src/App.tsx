import { Navigate, Outlet, Route, Routes } from 'react-router-dom';
import { Layout } from './components/Layout';
import { Dashboard } from './pages/Dashboard';
import { Knowledge } from './pages/Knowledge';
import { NewTicket } from './pages/NewTicket';
import { Placeholder } from './pages/Placeholder';
import { TicketDetail } from './pages/TicketDetail';
import { Tickets } from './pages/Tickets';
import { Login } from './pages/Login';

function Protected() { const demo = import.meta.env.MODE === 'test' || import.meta.env.VITE_DEMO_MODE === 'true'; return demo || localStorage.getItem('supportflow_token') ? <Outlet/> : <Navigate to="/login" replace/>; }
export default function App() { return <Routes><Route path="login" element={<Login/>}/><Route element={<Protected/>}><Route element={<Layout/>}><Route index element={<Dashboard/>}/><Route path="tickets" element={<Tickets/>}/><Route path="tickets/new" element={<NewTicket/>}/><Route path="tickets/:code" element={<TicketDetail/>}/><Route path="knowledge" element={<Knowledge/>}/><Route path="team" element={<Placeholder title="Equipo"/>}/><Route path="reports" element={<Placeholder title="Informes"/>}/><Route path="admin" element={<Placeholder title="Administración"/>}/><Route path="settings" element={<Placeholder title="Configuración"/>}/><Route path="*" element={<Navigate to="/" replace/>}/></Route></Route></Routes>; }

