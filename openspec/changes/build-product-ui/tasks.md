# Tasks

## 1. Foundation: palette, layout and navigation

- [x] 1.1 Set indigo primary and status colors in the theme tokens (`resources/css/app.css`) and verify light/dark render via `npm run build`
- [x] 1.2 Update sidebar to Dashboard, Spaces, Billing, Settings and remove starter-kit footer links; verify with a feature test that the authenticated page props/routes exist and visually in the browser
- [x] 1.3 Add shared flash message display and a reusable empty-state component; verify a flash shows after an action in a feature test of the shared props

## 2. Marketing home and auth branding

- [x] 2.1 Replace `welcome.tsx` with hero, features, pricing (limits from `config/plans.php` passed as props) and CTAs; verify a Pest test that `/` renders the new page with plan limits and not default content
- [x] 2.2 Apply product branding and cross-links to auth layouts/pages; verify existing auth tests still pass

## 3. Spaces management

- [x] 3.1 Add `SpacePolicy`, `SpaceController`, form requests and routes for index/create/store/edit/update/destroy using `CreateSpace`; verify Pest tests for owner scoping, forbidden access to others' spaces, duplicate slug, limit refusal and public ID stability
- [x] 3.2 Build spaces index, create/edit form (theme, rating, field configuration) and delete confirmation pages with limit-reached upgrade prompt; verify in browser and via Inertia component assertions in the tests

## 4. Public collection flow

- [x] 4.1 Add `/s/{slug}` controller and submission endpoint using `SubmitTestimonial`, including photo upload and friendly full-space handling; verify Pest tests for configured/required fields, success, not-found and full space
- [x] 4.2 Build the collection page and form with themes, conditional fields, rating input, consent, thank-you and unavailable states; verify in browser against a seeded space

## 5. Testimonial moderation

- [x] 5.1 Add nested testimonial controller with list filters and favorite/hide/Wall of Love/delete actions; verify Pest tests for consent guard, owner-only access and each toggle
- [x] 5.2 Build the testimonial manager page with filters, badges, disabled Wall of Love control and delete confirmation; verify in browser

## 6. Embed configuration and public wall

- [x] 6.1 Add embed settings controller/request and public `/embed/{public_id}` route (framing allowed) returning only visible testimonials without emails; verify Pest tests for validation, email absence and visibility filtering
- [x] 6.2 Build embed settings screen with live preview, snippet and public Wall of Love component for masonry and carousel; verify in browser

## 7. Dashboard analytics

- [x] 7.1 Add `DashboardController` using `ComputeOwnerAnalytics` with period filter and plan usage props; verify Pest tests for totals, owner scoping, period values and empty state
- [x] 7.2 Build the dashboard with stat cards, period selector, SVG daily chart and empty state; verify in browser

## 8. Billing and plan-limit states

- [x] 8.1 Add `BillingController` for page, checkout and portal redirect with not-configured handling and `STRIPE_PRO_PRICE_ID` config; verify Pest tests with faked Cashier for Free, Pro, cancelled-in-period and over-limit states
- [x] 8.2 Build billing page and shared upgrade-prompt component used on spaces and dashboard; verify in browser

## 9. Integration checks

- [x] 9.1 Regenerate Wayfinder routes, run `npm run build`, `vendor/bin/pint --dirty --format agent`, and the new feature tests; verify all pass
- [ ] 9.2 Walk through register → create space → submit testimonial → moderate → embed → dashboard → billing locally and confirm every screen is reachable from navigation
