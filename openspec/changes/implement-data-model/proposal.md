# Proposal

## Why

The app has authentication but no domain data: nothing stores spaces, testimonials, embed settings or billing state. Every feature in the PRD (collection forms, inbox, Wall of Love, embeds, analytics, plans) depends on these tables and rules, so the model planned in `docs/data-model.md` must exist first.

## What Changes

- Add `spaces` table and `Space` model: ULID `public_id`, editable unique `slug`, `Theme` enum, JSON `field_configuration` keyed by a backed enum, ratings toggle.
- Add `testimonials` table and `Testimonial` model with denormalized submitter fields (lowercased, trimmed email), flag booleans, optional rating and profile photo path, plus the three planned indexes.
- Add `embed_configurations` table and model, one row per space, created with defaults when the space is created.
- Add Cashier tables and columns (`subscriptions`, `subscription_items`, user billing columns) and a `stripe_webhook_events` table for idempotency.
- Add `config/plans.php` with Free and Pro limits, `User::currentPlan()` derived from subscription state, and transactional, row-locked limit checks for space creation and testimonial submission.
- Add a `publiclyVisible` scope (`is_wall_of_love AND NOT is_hidden AND consent_given`) and reject Wall of Love toggles on non-consented testimonials.
- Hard delete with cascading foreign keys; a deletion hook removes profile photo files.
- Add factories and seeders for all new models.
- Analytics are computed live from `testimonials`; no rollup tables.

## Capabilities

### New Capabilities
- `space-management`: spaces, slugs, public IDs, themes, field configuration, ownership and cascading deletion.
- `testimonial-storage`: testimonial records, submitter data normalization, flags, ratings, profile photo paths and file cleanup.
- `testimonial-visibility`: derived public visibility and the consent rule for Wall of Love.
- `embed-configuration`: per-space embed settings created with defaults.
- `plan-limits`: derived plan, central limits, concurrency-safe enforcement and grandfathering.
- `billing-webhook-events`: Cashier-backed subscription state and idempotent Stripe webhook event records.
- `testimonial-analytics`: live-computed counts, unique submitters, period filters and zero-filled daily series.

### Modified Capabilities

None. There are no existing specs.

## Impact

- **Database:** new migrations for spaces, testimonials, embed_configurations, stripe_webhook_events, and Cashier's published migrations.
- **Code:** new models, enums (`Theme`, field-configuration keys, embed layout), factories and seeders under `app/` and `database/`; `User` gains the `Billable` trait, `spaces()` and `currentPlan()`; new `config/plans.php`.
- **Dependencies:** `laravel/cashier` is already in `composer.json`; no new packages.
- **Out of scope:** controllers, routes, UI, Stripe checkout and webhook handlers, rate limiting and honeypot. This change covers the data layer and its rules only.
