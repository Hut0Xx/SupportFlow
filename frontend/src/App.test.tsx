import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { describe, expect, it } from 'vitest';
import App from './App';

const renderApp = (route = '/') => render(<QueryClientProvider client={new QueryClient({ defaultOptions: { queries: { retry: false } } })}><MemoryRouter initialEntries={[route]}><App/></MemoryRouter></QueryClientProvider>);
describe('SupportFlow', () => {
  it('muestra el panel en español', async () => { renderApp(); expect(screen.getByRole('heading', { name: /Resumen de soporte/i })).toBeInTheDocument(); expect(await screen.findByText('37')).toBeInTheDocument(); });
  it('permite abrir la lista de tickets', async () => { renderApp('/tickets'); expect(screen.getByRole('heading', { name: 'Tickets' })).toBeInTheDocument(); expect(await screen.findByText('SUP-1048')).toBeInTheDocument(); });
});

