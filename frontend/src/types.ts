export type TicketStatus = 'nuevo' | 'abierto' | 'pendiente' | 'resuelto' | 'cerrado';
export type Priority = 'Baja' | 'Media' | 'Alta' | 'Urgente';
export interface Ticket { id: number; code: string; title: string; requester: string; category: string; priority: Priority; status: TicketStatus; assignee: string | null; team: string; updatedAt: string; sla: 'ok' | 'risk' | 'breached'; description?: string; }
export interface DashboardMetrics { open: number; firstResponse: string; resolution: string; sla: number; delta: { open: number; response: number; resolution: number; sla: number } }

