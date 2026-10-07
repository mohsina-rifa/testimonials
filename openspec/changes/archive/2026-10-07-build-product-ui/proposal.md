## Why

The backend domain (spaces, testimonials, visibility, embed configuration, analytics, plan limits, Stripe billing) exists as models, actions and specs, but the running app still shows the default Laravel welcome page and a placeholder dashboard. There are no routes or screens for any product functionality, so nothing can be used locally.

## What Changes

- Replace the welcome page with a public marketing home page (hero, features, pricing for Free/Pro, calls to action to register/log in).
- Restyle the existing auth entry points (login, register, forgot/reset password, verify email) with product branding; keep Fortify behavior.
- Replace the placeholder dashboard with an analytics dashboard: totals, unique submitters, 7/30/90/all-time period filter, zero-filled daily chart, plan usage summary.
- Add spaces management: list, create (with plan-limit state), edit (title, subtitle, ask, slug, theme, rating toggle, optional field enable/required), delete with confirmation.
- Add a public collection page per space (by slug) with a submission form driven by the space's field configuration, consent checkbox, thank-you state, and a friendly "unavailable" state when the space is full.
- Add a testimonial manager per space: list, favorite, consent display, Wall of Love toggle (blocked in UI without consent), hide/unhide, delete.
- Add embed configuration screen per space (layout, dark mode, animation, background color, show rating/company/photo) with a live preview and copyable embed snippet, plus a public embed/Wall of Love view that never exposes submitter email.
- Add a billing page: current plan, limits and usage, upgrade (Stripe Checkout), manage/cancel via billing portal, cancelled-but-active-period state; upgrade prompts when limits are reached.
- Add thin controllers, form requests and named routes that expose the existing actions/models to these screens, and update sidebar/header navigation.
- Use a standard palette (Tailwind neutral/slate grays, one indigo primary, green/amber/red status colors) with light and dark mode via existing theme tokens.

## Capabilities

### New Capabilities

- `marketing-site`: Public home page and pricing presentation.
- `dashboard-analytics-ui`: Authenticated dashboard presenting owner analytics and plan usage.
- `space-management-ui`: Screens and routes to list, create, edit and delete spaces, including limit states.
- `testimonial-collection`: Public per-space collection page and submission flow.
- `testimonial-moderation-ui`: Owner screens to review testimonials and control favorite, Wall of Love and hidden state.
- `embed-ui`: Embed configuration screen, snippet, preview and public embed view.
- `billing-ui`: Plan, usage, upgrade and subscription management screens.
- `app-navigation`: Authenticated navigation structure and consistent UI palette.

### Modified Capabilities

None. Existing specs (space-management, testimonial-storage, testimonial-visibility, embed-configuration, testimonial-analytics, plan-limits, billing-webhook-events) are consumed unchanged.

## Impact

- Backend: new controllers, form requests and routes in `routes/web.php`; reuse `CreateSpace`, `SubmitTestimonial`, `ComputeOwnerAnalytics`; Cashier checkout/portal calls. Authorization so owners only access their own spaces. Requires `STRIPE_KEY`/`STRIPE_SECRET` and a Pro price ID config for billing actions; without them the billing UI shows a not-configured state.
- Frontend: new Inertia React pages and components under `resources/js`, updated layout/sidebar, regenerated Wayfinder routes.
- Tests: Pest feature tests for new routes, authorization and limit/consent behavior.
- No new dependencies assumed; a chart is built with plain SVG/CSS unless approved otherwise.
- Out of scope: new domain rules, Stripe webhook changes, email notifications, image upload processing beyond the existing profile photo path.
