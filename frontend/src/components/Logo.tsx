export function Logo({ compact = false }: { compact?: boolean }) {
  return <div className="flex items-center gap-3">
    <div className="relative grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-electric-500 shadow-[0_8px_20px_rgba(22,119,255,.3)]" aria-hidden="true">
      <span className="h-3.5 w-4 rounded-[4px] border-2 border-white" />
      <span className="absolute bottom-[8px] left-[10px] h-1.5 w-1.5 rotate-45 border-b-2 border-l-2 border-white" />
    </div>
    {!compact && <div><div className="text-[15px] font-bold tracking-tight">SupportFlow</div><div className="text-[9px] font-bold uppercase tracking-[.16em] text-slate-400">Mesa de ayuda</div></div>}
  </div>;
}

