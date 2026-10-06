import type { DashboardMetrics, Ticket } from '../types';

export const metrics: DashboardMetrics = { open: 37, firstResponse: '51 min', resolution: '7,8 h', sla: 91.6, delta: { open: 6, response: -4, resolution: 9, sla: -1.8 } };
export const tickets: Ticket[] = [
  { id: 1048, code: 'SUP-1048', title: 'El webhook de Stripe devuelve 401 desde las 09:15', requester: 'Laura Martín', category: 'Integraciones', priority: 'Urgente', status: 'abierto', assignee: 'Marta Ruiz', team: 'Integraciones', updatedAt: '2026-10-05T08:42:00Z', sla: 'risk', description: 'Desde las 09:15 todos los eventos invoice.paid reciben un 401. Hemos rotado la clave esta mañana; hay 143 facturas pendientes de confirmar.' },
  { id: 1047, code: 'SUP-1047', title: 'La invitación a usuarios caduca antes de 24 horas', requester: 'Diego Santos', category: 'Cuenta', priority: 'Alta', status: 'nuevo', assignee: null, team: 'Soporte', updatedAt: '2026-10-05T08:25:00Z', sla: 'ok' },
  { id: 1046, code: 'SUP-1046', title: 'El CSV de marzo no incluye la columna CIF', requester: 'Nora García', category: 'Datos', priority: 'Media', status: 'pendiente', assignee: 'Álex Gil', team: 'Datos', updatedAt: '2026-10-05T07:50:00Z', sla: 'ok' },
  { id: 1045, code: 'SUP-1045', title: 'La factura 2026-091 aparece cobrada dos veces', requester: 'Óscar Vega', category: 'Facturación', priority: 'Alta', status: 'abierto', assignee: 'Sara León', team: 'Facturación', updatedAt: '2026-10-05T07:12:00Z', sla: 'breached' },
  { id: 1044, code: 'SUP-1044', title: 'Activar SAML para el dominio ventas.lumen.test', requester: 'Elena López', category: 'Seguridad', priority: 'Media', status: 'resuelto', assignee: 'Marta Ruiz', team: 'Plataforma', updatedAt: '2026-10-04T16:20:00Z', sla: 'ok' },
  { id: 1043, code: 'SUP-1043', title: 'El informe semanal agota el tiempo a los 30 segundos', requester: 'Pablo Núñez', category: 'Rendimiento', priority: 'Baja', status: 'cerrado', assignee: 'Álex Gil', team: 'Plataforma', updatedAt: '2026-10-04T14:05:00Z', sla: 'ok' },
];

