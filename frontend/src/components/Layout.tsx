import { BookOpen, CircleHelp, Inbox, LayoutDashboard, Menu, Plus, X } from 'lucide-react';
import { useState } from 'react';
import { NavLink, Outlet } from 'react-router-dom';
import { Logo } from './Logo';
import { ThemeToggle } from './ThemeToggle';
import { cn } from '../lib/utils';

const mainNav = [
  { to: '/', label: 'Panel', icon: LayoutDashboard }, { to: '/tickets', label: 'Tickets', icon: Inbox, count: 12 }, { to: '/knowledge', label: 'Base de conocimiento', icon: BookOpen },
];
export function Layout() {
  const [open, setOpen] = useState(false);
  const nav = (items: typeof mainNav) => items.map(({ to, label, icon: Icon, ...item }) => <NavLink key={to} to={to} onClick={() => setOpen(false)} className={({ isActive }) => cn('flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', isActive ? 'bg-blue-50 text-electric-600 dark:bg-blue-950/50 dark:text-blue-300' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white')}><Icon className="h-[18px] w-[18px]" /><span className="flex-1">{label}</span>{'count' in item && <span className="rounded-full bg-electric-500 px-2 py-0.5 text-[10px] font-bold text-white">{item.count}</span>}</NavLink>);
  return <div className="min-h-screen bg-[#f6f8fb] dark:bg-[#07111f]">
    {open && <button aria-label="Cerrar menú" className="fixed inset-0 z-30 bg-slate-950/40 lg:hidden" onClick={() => setOpen(false)} />}
    <aside className={cn('fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-200 bg-white p-4 transition-transform dark:border-slate-800 dark:bg-navy-950 lg:translate-x-0', open ? 'translate-x-0' : '-translate-x-full')}>
      <div className="flex items-center justify-between px-2 py-2"><Logo /><button onClick={() => setOpen(false)} className="lg:hidden"><X /></button></div>
      <div className="mt-6 flex items-center gap-2 rounded-xl border border-slate-200 p-2 dark:border-slate-800"><div className="grid h-8 w-8 place-items-center rounded-lg bg-navy-900 text-xs font-bold text-white">LN</div><div className="min-w-0 flex-1"><div className="truncate text-xs font-semibold">Lumen Norte</div><div className="text-[10px] text-slate-400">Entorno de demostración</div></div></div>
      <nav className="mt-6 space-y-1" aria-label="Navegación principal">{nav(mainNav)}</nav>
      <div className="mt-auto"><a href="https://github.com/Hut0Xx/SupportFlow" target="_blank" rel="noreferrer" className="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"><CircleHelp className="h-[18px] w-[18px]" />Sobre este proyecto</a></div>
      <div className="mt-4 flex items-center gap-3 border-t border-slate-200 px-2 pt-4 dark:border-slate-800"><div className="grid h-9 w-9 place-items-center rounded-full bg-blue-600 text-xs font-bold text-white">HC</div><div className="min-w-0 flex-1"><div className="truncate text-xs font-semibold">Hugo Coarasa</div><div className="truncate text-[10px] text-slate-400">Administrador</div></div></div>
    </aside>
    <div className="lg:pl-64"><header className="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-xl dark:border-slate-800 dark:bg-navy-950/90 sm:px-7"><button className="lg:hidden" onClick={() => setOpen(true)} aria-label="Abrir menú"><Menu /></button><span className="hidden text-xs font-medium text-slate-400 sm:block">Lumen Norte · Soporte</span><div className="ml-auto flex items-center gap-1"><ThemeToggle /><NavLink to="/tickets/new" className="btn-primary ml-2"><Plus className="h-4 w-4" /><span className="hidden sm:inline">Nuevo ticket</span></NavLink></div></header><main><Outlet /></main></div>
  </div>;
}

