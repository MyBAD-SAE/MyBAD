# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

MyBAD — Application de gestion des scores et du classement pour des cours de Badminton. All UI text is in French.

## Commands

```bash
composer run dev          # Starts all dev servers (Laravel, queue, Pail logs, Vite)
composer run setup        # Full project setup from scratch (install, .env, key, migrate, npm)
composer run test         # Clear config + run PHPUnit tests
npm run build             # Production Vite build
php artisan pint          # Code formatting (Laravel Pint)
php artisan migrate       # Run pending migrations
```

Run a single test file:
```bash
php artisan test --filter=ExampleTest
```

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue 3.4 + Inertia.js 2 (SPA mode, no SSR)
- **Styling**: Tailwind CSS 4 with `@tailwindcss/forms` plugin + **shadcn-vue** (UI components)
- **UI Components**: shadcn-vue (new-york style) — add components via `npx shadcn-vue@latest add <component>`
- **Build**: Vite 7
- **Database**: SQLite (default), sessions/cache/queue all database-backed
- **Auth**: Laravel Breeze (session-based)
- **Routing**: Ziggy provides Laravel `route()` helper in Vue
- **Debugging**: Laravel Telescope (`/telescope`)

## Architecture

### Data Flow
Controllers return `Inertia::render('Page/Name', $data)` → Vue page receives data as props. Forms use `useForm()` from `@inertiajs/vue3` for submissions.

### Backend
- **Controllers**: `app/Http/Controllers/` — ProfileController + Auth/ controllers (Breeze)
- **Models**: `app/Models/User.php` — standard Breeze user model
- **Middleware**: `HandleInertiaRequests` shares auth user and flash messages with all pages
- **Providers**: `AppServiceProvider` (Vite prefetch), `TelescopeServiceProvider`

### Frontend
- **Pages**: `resources/js/Pages/` — Auth/ (Breeze auth pages), Dashboard, Welcome, Profile/
- **Layouts**: `AuthenticatedLayout.vue` (nav + user dropdown), `GuestLayout.vue` (centered card for auth)
- **Components**: `resources/js/Components/` — Breeze defaults (PrimaryButton, SecondaryButton, DangerButton, TextInput, InputLabel, InputError, Checkbox, Modal, Dropdown, DropdownLink, NavLink, ResponsiveNavLink, ApplicationLogo)
- **shadcn-vue UI**: `resources/js/Components/ui/` — shadcn-vue components (Button, etc.). Config in `components.json`

### CSS Tokens
`resources/css/app.css` uses shadcn-vue CSS variables (oklch) via `@theme inline`. **Couleur principale : `#27BDAE`** (vert turquoise MyBAD), utilisée pour `--primary` et `--ring`. Semantic tokens: `--background`, `--foreground`, `--card`, `--popover`, `--muted`, `--accent`, `--destructive`, `--border`, `--input`, `--ring`.

### Alias
`@` resolves to `resources/js/` (configured in vite.config.js and tsconfig.json).

## Git

- Ne jamais mentionner Claude dans les messages de commit (pas de "Co-Authored-By", pas de référence à Claude/AI).
