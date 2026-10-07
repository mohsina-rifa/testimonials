# Tasks

## 1. Billing foundation

- [ ] 1.1 Verify the existing Cashier migrations and `Billable` trait cover `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at`, `subscriptions` and `subscription_items` with no `plan` column; verify with `php artisan migrate:fresh` and `database-schema`
- [ ] 1.2 Create `stripe_webhook_events` migration and `StripeWebhookEvent` model (unique `stripe_event_id`, `event_type`, `processed_at`), then add Pest tests for recording an event and rejecting a duplicate ID (billing-webhook-events)

## 2. Enums and plan limits

- [ ] 2.1 Create enums `Plan`, `Theme`, `EmbedLayout` and `SpaceField` in `app/Enums`; verify with `php artisan about` loading and a unit test per enum's cases
- [ ] 2.2 Add `config/plans.php` (Free 3/100, Pro 25/1,000) and `User::currentPlan()` from subscription state; verify with Pest tests for no subscription, active, ended, and cancelled-within-period (plan-limits)

## 3. Spaces

- [ ] 3.1 Create `spaces` migration, `Space` model and factory via `php artisan make:model` (ULID `public_id`, unique slug, `Theme` cast, JSON `field_configuration`, cascade FK, `user_id` index); verify with `database-schema`
- [ ] 3.2 Add `User::spaces()`, `Space::user()`, and the field-configuration accessor defaulting missing keys to disabled and optional, rejecting unknown keys; verify with Pest tests for ownership, public ID immutability, slug uniqueness, duplicate titles, default theme and field defaults (space-management)

## 4. Embed configuration

- [ ] 4.1 Create `embed_configurations` migration, model and factory with the documented defaults and unique `space_id`; add `Space::embedConfiguration()` and create it when a space is created
- [ ] 4.2 Verify with Pest tests for default values on a new space, second configuration rejected, invalid layout rejected, and removal with the space (embed-configuration)

## 5. Testimonials

- [ ] 5.1 Create `testimonials` migration (all columns, three indexes, cascade FK), `Testimonial` model and factory with states for consent, favorite, Wall of Love and hidden; verify with `database-schema`
- [ ] 5.2 Implement email trimming and lowercasing, rating validation range, flag defaults and the `profile_photo_url` accessor; verify with Pest tests (testimonial-storage)
- [ ] 5.3 Implement photo file removal when a testimonial, space or user is deleted, using Eloquent hooks that run before database cascades; verify with Pest tests using `Storage::fake('public')` at each level
- [ ] 5.4 Verify disabled-field and ratings-off retention with Pest tests confirming stored company and rating values are unchanged

## 6. Visibility

- [ ] 6.1 Add the `publiclyVisible` scope and the consent guard rejecting Wall of Love without consent; verify with Pest tests for the four-row visibility table from the data model doc and the rejected toggle (testimonial-visibility)
- [ ] 6.2 Verify public output excludes submitter email with a Pest test on the public-facing serialization

## 7. Limit enforcement

- [ ] 7.1 Implement the transactional, row-locked limit action for space creation and testimonial submission with a friendly full-space result; verify with Pest tests for Free at limit, Pro under limit, and a full space (plan-limits)
- [ ] 7.2 Verify grandfathering with a Pest test that a downgraded user keeps all spaces and testimonials while new creation and submissions are refused

## 8. Analytics

- [ ] 8.1 Implement the analytics query for totals, unique submitters per owner, Wall of Love count and 7, 30, 90 day and all-time filters; verify with Pest tests built from the data model doc's sample data (testimonial-analytics)
- [ ] 8.2 Implement the zero-filled daily series in the app timezone; verify with a Pest test containing gap days and a pinned timezone

## 9. Seeders and integration

- [ ] 9.1 Add seeders reproducing the data model doc's sample users, spaces and testimonials; verify `php artisan db:seed` succeeds on a fresh database
- [ ] 9.2 Run `vendor/bin/pint --dirty --format agent` and the full set of new Pest files with `php artisan test --compact`, then ask the user to run the complete suite
