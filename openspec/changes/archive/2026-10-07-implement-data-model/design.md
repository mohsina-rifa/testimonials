# Design

## Context

See proposal.md for motivation and `docs/data-model.md` for the full planned model. Current state:

- Only `User` exists under `app/Models`; there are no `app/Enums`, no domain factories and no `config/plans.php`.
- Cashier is installed (`laravel/cashier` ^16.8) and its migrations for customer columns, `subscriptions` and `subscription_items` are already present. `User` already uses `Billable`, so that part needs verification, not creation.
- Tests use Pest. Models use PHP 8 attributes (`#[Fillable]`, `#[Hidden]`) and a `casts()` method, so new models follow that style.

## Goals / Non-Goals

**Goals:**

- A schema, models, enums, config, factories and seeders that satisfy every spec in this change.
- Business rules that live in the model layer (visibility scope, consent guard, limit checks), so later controllers cannot bypass them.

**Non-Goals:**

- Controllers, routes, UI, Stripe checkout or webhook handling, rate limiting and honeypot.
- Policies and authorization of who may edit a space; they arrive with the features that expose it.

## Decisions

1. **Limit enforcement in a service-style action, not model events.** A `SpaceLimitGuard`-style class (final naming left to implementation) runs `DB::transaction` with `lockForUpdate()` on the parent row, counts, then runs the insert callback. Alternative: a `creating` model event, rejected because the lock must wrap both the count and the insert, and events can't express that cleanly. Callers must go through the action; the spec scenarios test that path.
2. **Plan derivation via `subscribed('default')`.** `User::currentPlan()` returns a `Plan` backed enum (`Free`, `Pro`) using Cashier's subscription state, which already treats the grace period as subscribed. Limits come from `config/plans.php` keyed by the enum value. Alternative: a stored `plan` column, rejected because it can drift from Stripe.
3. **Enums.** `Theme` (`light`, `dark`, `minimal`), `EmbedLayout` (`masonry`, `carousel`), `SpaceField` (`company`, `social_link`, `profile_photo`) and `Plan`, in `app/Enums`, TitleCase keys per project rules.
4. **`field_configuration` as a JSON column with an accessor.** `Space` exposes a method that resolves a `SpaceField` to an `enabled`/`required` pair, defaulting a missing key to disabled and optional. Writes are filtered to known keys. Alternative: a child table, rejected as over-modelled for a fixed three-field set.
5. **Defaults created in the model, not a DB trigger.** `Space::booted()` sets `public_id` (`HasUlids` on the `public_id` column only, via `uniqueIds()`) and a `created` hook creates the `EmbedConfiguration` with column defaults. Cascade deletes run at the database level through foreign keys.
6. **Photo cleanup must survive DB cascades.** Foreign-key cascades do not fire model events, so a `Testimonial` `deleted` hook alone would miss space and user deletion. `Space` gets a `deleting` hook that removes photos for its testimonials, and `User` a `deleting` hook that deletes each space through Eloquent. Alternative: a scheduled orphan sweeper, rejected as eventually-consistent privacy deletion.
7. **Email normalization via an attribute mutator** on `submitter_email` (trim, lowercase).
8. **Visibility.** `scopePubliclyVisible` encodes the three-flag rule. The consent rule is enforced in a `saving` hook that throws a validation exception when `is_wall_of_love` is true without `consent_given`. Alternative: only checking in form requests, rejected because the spec requires server-side enforcement everywhere.
9. **Analytics as a query class** returning totals and a zero-filled series built in PHP from a grouped query on `created_at` converted to the app timezone. Alternative: SQL date functions, rejected to keep the query simple and independent of MySQL date functions.
10. **Webhook events** use a plain model with a unique index on `stripe_event_id`; idempotency is "insert or catch the unique violation".
11. **Indexes** follow the data model doc: `(space_id, is_favorite, created_at)`, `(space_id, created_at)`, `(space_id, submitter_email)`.

## Risks / Trade-offs

- [Callers bypass the limit action] → Keep the action the only creation path in later features, and cover the contract with tests.
- [Cascade deletes skip photo removal] → Hooks on `Space` and `User` delete through Eloquent before the database cascade runs; tested for each level.
- [Test database differs from production] → Tests run on MySQL (`testimonials_testing`, set in `phpunit.xml`) so `lockForUpdate()` and the transaction behave as in production.
- [Timezone bucketing] → Series are built from UTC timestamps converted to the app timezone; tests pin the timezone.
- [Existing Cashier migrations were generated before this change] → Verify they match the data model doc rather than regenerating them.

## Migration Plan

Run the new migrations after the existing ones; they only add tables. Rollback is `migrate:rollback` for the new batch. No data exists yet, so no backfill is needed.
