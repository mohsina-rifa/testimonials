# Design

## Context

- Sidebar layout (`app-sidebar-layout.tsx`, `app-sidebar.tsx`) has a fixed list: Dashboard, Spaces, Billing, Settings. Space screens use in-page `SpaceTabs` (Testimonials, Settings, Embed).
- `DashboardController` renders owner-wide analytics via `ComputeOwnerAnalytics`; `config/fortify.php` has `home => /dashboard`.
- `use-appearance.tsx` already supports light/dark/system with cookie + localStorage and `HandleAppearance` middleware, but the UI only exposes it on the appearance settings page.
- Routes are nested under `spaces/{space}/...`; there is no `space` route-level layout context on the client.

## Goals / Non-Goals

**Goals:**

- Space-centric navigation driven by the current route.
- Reuse the existing appearance hook for the toggle.

**Non-Goals:**

- No change to billing, collection pages, or embed behavior.
- No redesign of the palette; existing tokens are already the standard palette.

## Decisions

- **Space context via shared Inertia props.** `HandleInertiaRequests` shares the owner's spaces (`id`, `title`) lazily for authenticated users. The current space is taken from the page's `space` prop (already passed by space pages). Alternative: a separate fetch from the switcher; rejected as extra requests.
- **Per-space dashboard at `spaces/{space}/dashboard`** (named `spaces.dashboard`), reusing the `dashboard` page and `ComputeOwnerAnalytics` with an optional space filter (owner scoping retained). Plan usage moves to the spaces index (it already lists limits). `/dashboard` and `DashboardController` route are deleted; no redirect, so it 404s.
- **Inbox = existing testimonials route** relabelled; **Space = existing edit route**. No URL changes to avoid breaking links.
- **Sidebar swaps content by context:** global group (Spaces, Billing, Settings) always; a space group (Dashboard, Inbox, Embed, Space) when a space is current. A space switcher dropdown sits in the sidebar header below the brand and navigates to the same section of the chosen space, falling back to its Dashboard. `SpaceTabs` is removed from pages in favour of the sidebar.
- **Theme toggle** is a light/dark button in the sidebar footer / user area calling `updateAppearance`. "System" stays the default until the user chooses.
- **Landing:** set Fortify `home` to `route('spaces.index')` path (`/spaces`), so the host stays the one the user is on instead of hard-coding `anish0m.testimonials:8000`.
- **Branding:** the logo already reads the shared `name` prop (from `APP_NAME`); remove starter-kit footer links (`nav-footer` repo/docs links), set `APP_NAME` in `.env.example` and local `.env`, and replace any remaining "Laravel Starter Kit" strings.

## Risks / Trade-offs

- Removing `/dashboard` breaks bookmarks and tests → update tests; intentional 404.
- Wayfinder output goes stale → regenerate and run type checks.
- Pages without a `space` prop show only the global group → verify all space pages pass `space` (id and title).
