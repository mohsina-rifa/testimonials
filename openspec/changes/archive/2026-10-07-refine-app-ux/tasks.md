# Tasks

## 1. Backend routes and landing

- [x] 1.1 Add `spaces/{space}/dashboard` (`spaces.dashboard`), owner-authorized, scoping analytics to the space; remove `/dashboard` route. Verify with a feature test: owner sees only that space's totals, other owner is refused, `/dashboard` returns 404.
- [x] 1.2 Add optional space filter to `ComputeOwnerAnalytics`; verify with an updated `DashboardAnalyticsTest`.
- [x] 1.3 Set Fortify `home` to `/spaces`; update auth tests (login, register, email verification) to assert redirect to `spaces.index`.
- [x] 1.4 Share the owner's spaces from `HandleInertiaRequests`; verify with a test that only the owner's spaces are shared and guests get none.
- [x] 1.5 Move plan usage summary onto the spaces index; verify a test shows "2 of 3 spaces used".

## 2. Navigation and theme UI

- [x] 2.1 Rework `app-sidebar.tsx`: global group (Spaces, Billing, Settings), space group (Dashboard, Inbox, Embed, Space) when a space is current, remove the Dashboard link and default footer links. Verify manually and with `npm run types:check`.
- [x] 2.2 Add a space switcher component keeping the current section; verify by switching spaces in the browser.
- [x] 2.3 Add a light/dark toggle using `use-appearance`; verify it applies instantly and persists after reload.
- [x] 2.4 Remove `SpaceTabs` from space pages and confirm every space page passes `space` (id, title); verify each page shows the space group.
- [x] 2.5 Update the dashboard page for per-space data and empty state; replace remaining `/dashboard` links (welcome page, header, passkey verify).

## 3. Branding and cleanup

- [x] 3.1 Set `APP_NAME` in `.env.example` (and `.env`) and replace "Laravel Starter Kit" strings; verify the sidebar brand and page titles show the app name.
- [x] 3.2 Regenerate Wayfinder routes, run `vendor/bin/pint --dirty --format agent`, `npm run types:check`, `npm run lint`, and the affected Pest tests.
