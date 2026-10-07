# Data Model — Minimal Testimonial SaaS

Derived from `docs/minimal-testimonial-saas-prd.md` through a decision-by-decision review. This document supersedes PRD section 14 where they differ.

## Sample data used to reason about the model

**Users**

| id  | name  | email          |
| --- | ----- | -------------- |
| 1   | Alice | alice@acme.com |
| 2   | Bob   | bob@studio.io  |

**Spaces** (each user owns two)

| id  | user_id | title            | slug            |
| --- | ------- | ---------------- | --------------- |
| 1   | 1       | Acme Product     | acme-product    |
| 2   | 1       | Acme Consulting  | acme-consulting |
| 3   | 2       | Bob's Course     | bobs-course     |
| 4   | 2       | Bob's Newsletter | bobs-newsletter |

**Testimonials**

| id  | space_id | name | email           | company | rating | text               |
| --- | -------- | ---- | --------------- | ------- | ------ | ------------------ |
| 1   | 1        | Dana | dana@gmail.com  | Globex  | 5      | "Love it!"         |
| 2   | 1        | Eli  | eli@initech.com | Initech | 4      | "Saved us hours"   |
| 3   | 2        | Dana | dana@gmail.com  | Globex  | 5      | "Great consulting" |
| 4   | 3        | Dana | dana@gmail.com  | NULL    | NULL   | "Best course"      |
| 5   | 3        | Fay  | fay@hooli.com   | Hooli   | 3      | "Pretty good"      |

Dana submits to three spaces owned by two users. She counts once toward Alice's unique submitters, and her data is never shared across owners.

## Decisions

| #   | Decision                                                                                                                                                                                                                    | Rationale                                                                                                                                                        |
| --- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | Submitter fields are denormalized on `testimonials`. The email is stored lowercased and trimmed.                                                                                                                            | There are no customer accounts. Renaming a submitter affects one testimonial. No cross-tenant leakage. Contact management is a non-goal.                         |
| 2   | Billing uses Laravel Cashier's tables. The plan is derived, never stored. Limits live in `config/plans.php`. We add our own `stripe_webhook_events` table.                                                                  | Cashier already covers checkout, portal, webhooks and signature verification. A derived plan can't drift, and lapsed subscriptions revert to Free automatically. |
| 3   | `spaces.field_configuration` is a JSON column, with a backed enum for the keys (`company`, `social_link`, `profile_photo`). A missing key means disabled and optional.                                                      | The field set is fixed and small, and is always read and written with its space.                                                                                 |
| 4   | `spaces.public_id` is an immutable ULID used by embeds. `embed_configurations` is one row per space, created with defaults when the space is created.                                                                       | Embeds survive slug edits. Internal IDs are not exposed. A row that always exists avoids null handling.                                                          |
| 5   | Hard delete for spaces and testimonials, with cascading foreign keys. Photo files are removed by a model deletion hook.                                                                                                     | Real privacy deletion. No slug-reuse or count complications. The UI carries strong confirmation (typed title for spaces).                                        |
| 6   | Booleans for `consent_given`, `is_favorite`, `is_wall_of_love` and `is_hidden`. Public visibility is derived by a `publiclyVisible` scope. Wall of Love toggles on non-consented testimonials are rejected (UI and server). | No drift from a stored visibility flag. One place to test the visibility rules. Avoids a state where the owner thinks something is published.                    |
| 7   | `spaces.theme` is a string cast to a `Theme` enum (`light`, `dark`, `minimal`), default `light`.                                                                                                                            | Themes are code-defined and not user-editable. Custom CSS is a non-goal.                                                                                         |
| 8   | `testimonials.profile_photo_path` (nullable) with a `profile_photo_url` accessor, on the `public` disk.                                                                                                                     | The storage disk or domain can change without a data migration. Deletion works by path.                                                                          |
| 9   | Analytics are computed live from `testimonials`. There are no rollup tables.                                                                                                                                                | Plan limits bound the row counts. No sync logic to keep consistent.                                                                                              |
| 10  | Users over their limit (for example after a downgrade) are grandfathered. Creation and submissions are blocked, and nothing is deleted or locked.                                                                           | Non-destructive, with no schema. Matches the PRD's limit-enforcement text.                                                                                       |
| 11  | `spaces.slug` is editable, unique and lowercase URL-safe. The edit form warns that old links stop working. There is no redirect history.                                                                                    | Simple, and typos are fixable. The `/s/` prefix avoids collisions with other routes.                                                                             |

## Tables

### users

Laravel and Fortify defaults plus Cashier's columns.

| column        | type                | notes            |
| ------------- | ------------------- | ---------------- |
| id            | bigint PK           |                  |
| name          | string              |                  |
| email         | string              | unique           |
| password      | string              | hashed           |
| stripe_id     | string, nullable    | Cashier, indexed |
| pm_type       | string, nullable    | Cashier          |
| pm_last_four  | string(4), nullable | Cashier          |
| trial_ends_at | timestamp, nullable | Cashier          |
| timestamps    |                     |                  |

There is no `plan` column. `User::currentPlan()` returns `free` or `pro` from the subscription state.

### subscriptions and subscription_items

Cashier's own tables, unchanged. Status, period end and cancel-at-period-end (`ends_at`) come from here.

### spaces

| column              | type       | notes                         |
| ------------------- | ---------- | ----------------------------- |
| id                  | bigint PK  |                               |
| user_id             | FK → users | cascade on delete, indexed    |
| public_id           | ulid       | unique, immutable             |
| title               | string     |                               |
| subtitle            | string     |                               |
| ask                 | text       | testimonial prompt            |
| slug                | string     | unique, lowercase, URL-safe   |
| theme               | string     | `Theme` enum, default `light` |
| rating_enabled      | boolean    |                               |
| field_configuration | json       | see below                     |
| timestamps          |            |                               |

`field_configuration` shape (name and email are always on and required, so they are not stored):

```json
{
    "company": { "enabled": true, "required": true },
    "social_link": { "enabled": false, "required": false },
    "profile_photo": { "enabled": true, "required": false }
}
```

### testimonials

| column             | type              | notes                                                                       |
| ------------------ | ----------------- | --------------------------------------------------------------------------- |
| id                 | bigint PK         |                                                                             |
| space_id           | FK → spaces       | cascade on delete                                                           |
| submitter_name     | string            | owner can rename                                                            |
| submitter_email    | string            | lowercased and trimmed                                                      |
| company_name       | string, nullable  |                                                                             |
| social_link        | string, nullable  |                                                                             |
| profile_photo_path | string, nullable  |                                                                             |
| testimonial_text   | text              |                                                                             |
| rating             | tinyint, nullable | 1–5, validated in the app; required only when the space has ratings enabled |
| consent_given      | boolean           | default false                                                               |
| is_favorite        | boolean           | default false                                                               |
| is_wall_of_love    | boolean           | default false                                                               |
| is_hidden          | boolean           | default false                                                               |
| timestamps         |                   |                                                                             |

Indexes:

- `(space_id, is_favorite, created_at)`: inbox ordering (favorites first, newest first).
- `(space_id, created_at)`: the analytics chart.
- `(space_id, submitter_email)`: unique submitters.

Public visibility (derived, never stored): `is_wall_of_love AND NOT is_hidden AND consent_given`.

| id  | name | consent | favorite | wall_of_love | hidden | publicly visible?                              |
| --- | ---- | ------- | -------- | ------------ | ------ | ---------------------------------------------- |
| 1   | Dana | ✅      | ✅       | ✅           | ❌     | Yes                                            |
| 2   | Eli  | ✅      | ❌       | ✅           | ✅     | No (hidden)                                    |
| 3   | Dana | ❌      | ❌       | ✅           | ❌     | No (no consent; the server rejects this state) |
| 5   | Fay  | ✅      | ❌       | ❌           | ❌     | No (not on wall)                               |

### embed_configurations

| column             | type                                 | default           |
| ------------------ | ------------------------------------ | ----------------- |
| id                 | bigint PK                            |                   |
| space_id           | FK → spaces, unique                  | cascade on delete |
| layout             | string (enum: `masonry`, `carousel`) | `masonry`         |
| dark_mode          | boolean                              | false             |
| animation_enabled  | boolean                              | true              |
| background_color   | string                               | `#ffffff`         |
| show_rating        | boolean                              | true              |
| show_company       | boolean                              | true              |
| show_profile_photo | boolean                              | true              |
| timestamps         |                                      |                   |

The row is created with these defaults when the space is created.

### stripe_webhook_events

| column          | type      | notes                   |
| --------------- | --------- | ----------------------- |
| id              | bigint PK |                         |
| stripe_event_id | string    | unique, for idempotency |
| event_type      | string    |                         |
| processed_at    | timestamp |                         |

## Relationships

- User has many Spaces.
- Space belongs to a User, has many Testimonials, and has one EmbedConfiguration.
- Testimonial belongs to a Space.
- All foreign keys cascade on delete (user → spaces → testimonials and embed configuration).

## Plan limits

Defined centrally in `config/plans.php`.

| plan | max spaces | max testimonials per space |
| ---- | ---------- | -------------------------- |
| free | 3          | 100                        |
| pro  | 25         | 1,000                      |

- Checks are enforced server-side on space creation and testimonial submission, using the owning user's current plan.
- A user already over a limit (for example after a downgrade) keeps everything. Only new creation and submissions are blocked.
- The limit check and insert run in a transaction that locks the parent row (the user for spaces, the space for testimonials), so concurrent requests can't slip under the cap.
- Public visitors to a full space see a friendly unavailable message.

## Analytics

All computed live. No analytics tables.

- Total spaces: count of the user's spaces.
- Total testimonials: count across the user's spaces.
- Unique submitters: `COUNT(DISTINCT submitter_email)` across the user's spaces.
- Collected in period (7, 30, 90 days, or all time): a filter on `created_at`.
- Per-day chart: group by day, zero-filled for missing days.
- Wall of Love count: the number of testimonials with `is_wall_of_love`.
- Timestamps are stored in UTC, and daily buckets use the app timezone.

## Additional defaults

- If ratings are turned off later, existing ratings are kept but not shown.
- Disabling an optional field later hides it from the form but keeps stored values.
- Space titles need not be unique, since only the slug is.
- Public submission and embed endpoints are protected by rate limiting and a honeypot field in the app. These need no database columns.
- Submitter email is never exposed in embeds or public APIs.
