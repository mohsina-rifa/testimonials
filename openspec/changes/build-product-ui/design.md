# Design

## Context

The domain layer exists: `Space`, `Testimonial`, `EmbedConfiguration` models; `CreateSpace`, `SubmitTestimonial`, `ComputeOwnerAnalytics` actions; `Plan` enum with `config/plans.php`; Cashier on `User`. No product routes or controllers exist; `routes/web.php` only has the welcome, dashboard (static) and settings routes. Frontend is Inertia v3 + React with shadcn-style components in `resources/js/components/ui`, an app sidebar layout, auth layouts, and Wayfinder for typed routes. Fortify already serves auth.

## Goals / Non-Goals

**Goals:**
- Every implemented capability reachable and usable via screens locally.
- Reuse existing actions, layouts and ui components; keep business rules in the domain layer.
- Standard palette through existing CSS theme tokens.

**Non-Goals:**
- New domain rules, webhook changes, emails, new dependencies, image processing.

## Decisions

- **Thin controllers per resource** (`SpaceController`, `SpaceTestimonialController`, `EmbedConfigurationController`, `DashboardController`, `BillingController`, public `CollectionController` and `EmbedController`) plus Form Requests; they call existing actions. Alternative (closure routes) rejected for testability and authorization policies.
- **Authorization via a `SpacePolicy`** (owner only), applied with route model binding scoped to the authenticated user's spaces.
- **Routes**: authenticated `spaces` resource, nested `spaces/{space}/testimonials`, `spaces/{space}/embed`, `billing`; public `/s/{slug}` for collection, `/embed/{public_id}` for the Wall of Love. Public embed uses `public_id` so slug edits do not break embeds.
- **Public data shaping**: embed/collection props are built explicitly (no model serialization) so submitter email is never sent; embed uses the existing public-visibility query scope.
- **Limit handling**: UI reads plan/usage props for disabled states and upgrade prompts, but server still enforces via existing actions; `PlanLimitReachedException` is caught and returned as a flash/validation error or the friendly unavailable state.
- **Billing**: Cashier `newSubscription(...)->checkout()` and `redirectToBillingPortal()`. Price ID read from config/env (`STRIPE_PRO_PRICE_ID`); if keys/price missing, UI shows "billing unavailable". Needs approval for the new config key only (no new packages).
- **Chart**: plain SVG bar chart component fed by the zero-filled series; avoids a new charting dependency.
- **Embed preview**: client-side render of the same `WallOfLove` React component used by the public embed, fed with unsaved form state.
- **Palette**: use existing Tailwind/shadcn tokens, setting primary to indigo and keeping neutral, green/amber/red status variants in `badge`/`alert`.
- **Profile photo upload**: stored on the public disk through the submission request; path saved on the testimonial.

## Risks / Trade-offs

- Stripe flows cannot be fully exercised locally without keys; tests fake Cashier calls and the UI has a not-configured state.
- Embed served inside iframes may need header adjustments (frame options); the embed route must allow framing while the app routes do not.
- Plain SVG chart is less feature-rich than a library but avoids dependency approval.
- Scope is large; tasks are grouped so each slice (spaces, collection, moderation, embed, billing, dashboard) ships with its tests.
