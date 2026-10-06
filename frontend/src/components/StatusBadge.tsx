import { Circle } from 'lucide-react';
import type { Priority, TicketStatus } from '../types';

const statusMap: Record<TicketStatus, string> = { nuevo: 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300', abierto: 'bg-violet-50 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300', pendiente: 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300', resuelto: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300', cerrado: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' };
const priorityMap: Record<Priority, string> = { Baja: 'text-slate-500', Media: 'text-blue-600', Alta: 'text-orange-600', Urgente: 'text-rose-600' };
export function StatusBadge({ status }: { status: TicketStatus }) { return <span className={`chip capitalize ${statusMap[status]}`}><Circle className="h-2 w-2 fill-current" />{status}</span>; }
export function PriorityBadge({ priority }: { priority: Priority }) { return <span className={`inline-flex items-center gap-1.5 text-xs font-semibold ${priorityMap[priority]}`}><span className="h-1.5 w-1.5 rounded-full bg-current" />{priority}</span>; }

