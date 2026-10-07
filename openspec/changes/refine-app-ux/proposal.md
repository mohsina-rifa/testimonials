# Proposal

## Why

The app still looks like the Laravel starter kit: a global dashboard, starter-kit links and branding, and no way to move between spaces quickly. Since everything an owner does happens inside a space, navigation should be organised around spaces.

## What Changes

- Add a light/dark theme toggle in the authenticated layout (the existing appearance hook already supports light/dark/system).
- Add a space switcher to the sidebar so owners can jump between their spaces.
- **BREAKING**: Remove the central `/dashboard` page and route. Analytics move to a per-space Dashboard (`spaces/{space}/dashboard`), scoped to that space.
- **BREAKING**: Login, registration and email verification land on `/spaces` instead of `/dashboard`.
- When a space is selected, the sidebar shows space navigation: Dashboard, Inbox (the testimonials list), Embed, Space (the edit page). Replaces the Testimonials/Settings/Embed tabs.
- Remove the starter-kit default footer links from the sidebar and drop the global Dashboard link.
- Replace "Laravel Starter Kit" branding with `APP_NAME`.

## Capabilities

### New Capabilities
- `theme-toggle`: user-selectable light/dark mode in the authenticated UI using the standard palette.

### Modified Capabilities
- `app-navigation`: authenticated navigation, space sub-navigation, space switcher, post-auth landing at `/spaces`, branding.
- `dashboard-analytics-ui`: dashboard becomes per-space and the central dashboard is removed.

## Impact

- Backend: `routes/web.php`, `DashboardController` (becomes space-scoped), `ComputeOwnerAnalytics` (space scope), `config/fortify.php` (`home`), share the owner's spaces via `HandleInertiaRequests`.
- Frontend: `app-sidebar.tsx`, `nav-*`, `space-tabs.tsx`, `pages/dashboard.tsx`, new theme toggle and space switcher components, `pages/spaces/*` layouts, welcome/auth links to `/dashboard`.
- Config: `.env.example` `APP_NAME`.
- Tests: `DashboardTest`, `DashboardAnalyticsTest`, and auth tests asserting a `/dashboard` redirect.
- Wayfinder-generated routes must be regenerated.
