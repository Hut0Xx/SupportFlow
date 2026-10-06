import { BarChart3, Bell, BookOpen, ChevronsUpDown, CircleHelp, Inbox, LayoutDashboard, Menu, Plus, Search, Settings, ShieldCheck, Users, X } from 'lucide-react';
import { useState } from 'react';
import { NavLink, Outlet } from 'react-router-dom';
import { Logo } from './Logo';
import { ThemeToggle } from './ThemeToggle';
import { cn } from '../lib/utils';

const mainNav = [
  { to: '/', label: 'Panel', icon: LayoutDashboard }, { to: '/tickets', label: 'Tickets', icon: Inbox, count: 12 }, { to: '/knowledge', label: 'Base de conocimiento', icon: BookOpen },
];
const manageNav = [{ to: '/team', label: 'Equipo', icon: Users }, { to: '/reports', label: 'Informes', icon: BarChart3 }, { to: '/admin', label: 'Administración', icon: ShieldCheck }];

export function Layout() {
  const [open, setOpen] = useState(false);
  const nav = (items: typeof mainNav) => items.map(({ to, label, icon: Icon, ...item }) => <NavLink key={to} to={to} onClick={() => setOpen(false)} className={({ isActive }) => cn('flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', isActive ? 'bg-blue-50 text-electric-600 dark:bg-blue-950/50 dark:text-blue-300' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white')}><Icon className="h-[18px] w-[18px]" /><span className="flex-1">{label}</span>{'count' in item && <span className="rounded-full bg-electric-500 px-2 py-0.5 text-[10px] font-bold text-white">{item.count}</span>}</NavLink>);
  return <div className="min-h-screen bg-[#f6f8fb] dark:bg-[#07111f]">
    {open && <button aria-label="Cerrar menú" className="fixed inset-0 z-30 bg-slate-950/40 lg:hidden" onClick={() => setOpen(false)} />}
    <aside className={cn('fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-200 bg-white p-4 transition-transform dark:border-slate-800 dark:bg-navy-950 lg:translate-x-0', open ? 'translate-x-0' : '-translate-x-full')}>
      <div className="flex items-center justify-between px-2 py-2"><Logo /><button onClick={() => setOpen(false)} className="lg:hidden"><X /></button></div>
      <div className="mt-6 rounded-xl border border-slate-200 p-2 dark:border-slate-800"><button className="flex w-full items-center gap-2 text-left"><div className="grid h-8 w-8 place-items-center rounded-lg bg-navy-900 text-xs font-bold text-white">AC</div><div className="min-w-0 flex-1"><div className="truncate text-xs font-semibold">Acme Digital</div><div className="text-[10px] text-slate-400">Plan Business</div></div><ChevronsUpDown className="h-3.5 w-3.5 text-slate-400" /></button></div>
      <nav className="mt-6 space-y-1" aria-label="Navegación principal">{nav(mainNav)}</nav>
      <div className="mb-2 mt-6 px-3 text-[10px] font-bold uppercase tracking-[.14em] text-slate-400">Gestión</div><nav className="space-y-1">{nav(manageNav)}</nav>
      <div className="mt-auto space-y-1"><NavLink to="/settings" className="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"><Settings className="h-[18px] w-[18px]" />Configuración</NavLink><button className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"><CircleHelp className="h-[18px] w-[18px]" />Ayuda y recursos</button></div>
      <div className="mt-4 flex items-center gap-3 border-t border-slate-200 px-2 pt-4 dark:border-slate-800"><div className="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-blue-500 to-violet-500 text-xs font-bold text-white">HC</div><div className="min-w-0 flex-1"><div className="truncate text-xs font-semibold">Hugo Coarasa</div><div className="truncate text-[10px] text-slate-400">Administrador</div></div><ChevronsUpDown className="h-3.5 w-3.5 text-slate-400" /></div>
    </aside>
    <div className="lg:pl-64"><header className="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-xl dark:border-slate-800 dark:bg-navy-950/90 sm:px-7"><button className="lg:hidden" onClick={() => setOpen(true)} aria-label="Abrir menú"><Menu /></button><div className="relative hidden max-w-md flex-1 sm:block"><Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" /><input className="input py-2 pl-9" placeholder="Buscar tickets, personas o artículos…" aria-label="Buscar" /><span className="absolute right-2.5 top-2 rounded border border-slate-200 px-1.5 py-0.5 text-[9px] text-slate-400 dark:border-slate-700">⌘ K</span></div><div className="ml-auto flex items-center gap-1"><ThemeToggle /><button className="relative grid h-9 w-9 place-items-center rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Notificaciones"><Bell className="h-4.5 w-4.5" /><span className="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-navy-950" /></button><NavLink to="/tickets/new" className="btn-primary ml-2"><Plus className="h-4 w-4" /><span className="hidden sm:inline">Nuevo ticket</span></NavLink></div></header><main><Outlet /></main></div>
  </div>;
}

