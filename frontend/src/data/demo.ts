import type { DashboardMetrics, Ticket } from '../types';

export const metrics: DashboardMetrics = { open: 128, firstResponse: '42 min', resolution: '6,4 h', sla: 94.2, delta: { open: -8, response: -12, resolution: -5, sla: 2.4 } };
export const tickets: Ticket[] = [
  { id: 1048, code: 'SUP-1048', title: 'Error al sincronizar facturas con el ERP', requester: 'Laura Martín', category: 'Integraciones', priority: 'Urgente', status: 'abierto', assignee: 'Marta Ruiz', team: 'Integraciones', updatedAt: '2026-10-05T08:42:00Z', sla: 'risk', description: 'La sincronización nocturna falla desde el viernes y hay 143 facturas pendientes.' },
  { id: 1047, code: 'SUP-1047', title: 'No puedo invitar miembros al espacio', requester: 'Diego Santos', category: 'Cuenta', priority: 'Alta', status: 'nuevo', assignee: null, team: 'Soporte nivel 1', updatedAt: '2026-10-05T08:25:00Z', sla: 'ok' },
  { id: 1046, code: 'SUP-1046', title: 'Solicitud de exportación histórica', requester: 'Nora García', category: 'Datos', priority: 'Media', status: 'pendiente', assignee: 'Álex Gil', team: 'Datos', updatedAt: '2026-10-05T07:50:00Z', sla: 'ok' },
  { id: 1045, code: 'SUP-1045', title: 'Cobro duplicado en la última factura', requester: 'Óscar Vega', category: 'Facturación', priority: 'Alta', status: 'abierto', assignee: 'Sara León', team: 'Facturación', updatedAt: '2026-10-05T07:12:00Z', sla: 'breached' },
  { id: 1044, code: 'SUP-1044', title: 'Configurar SSO para el equipo comercial', requester: 'Elena López', category: 'Seguridad', priority: 'Media', status: 'resuelto', assignee: 'Marta Ruiz', team: 'Plataforma', updatedAt: '2026-10-04T16:20:00Z', sla: 'ok' },
  { id: 1043, code: 'SUP-1043', title: 'El panel tarda en cargar', requester: 'Pablo Núñez', category: 'Rendimiento', priority: 'Baja', status: 'cerrado', assignee: 'Álex Gil', team: 'Plataforma', updatedAt: '2026-10-04T14:05:00Z', sla: 'ok' },
];

