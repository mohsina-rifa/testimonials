# PRD — Minimal Testimonial SaaS

## 1. Product Overview

Build a minimal SaaS application inspired by Testimonial.to that allows businesses to create public testimonial collection pages, collect customer testimonials without requiring customer accounts, manage submissions, publish selected testimonials, and generate embeddable testimonial widgets.

The product follows a freemium subscription model with Stripe billing.

---

## 2. Goals

### Primary Goals

- Allow users to create an account and manage testimonial collection spaces.
- Allow customers to submit testimonials through public links without authentication.
- Allow users to review, organize, hide, favorite, delete, and publish testimonials.
- Allow users to create embeddable testimonial widgets.
- Provide basic collection analytics.
- Enforce free and paid plan limits.
- Support Stripe Checkout, subscriptions, and webhook-based billing synchronization.

### Non-Goals for MVP

- Video testimonials.
- Custom domains.
- Team accounts or multi-user workspaces.
- Custom CSS.
- API access.
- Multiple paid tiers.
- Advanced moderation workflows.
- AI-generated testimonial content.
- Importing testimonials from third-party platforms.

---

## 3. User Roles

### Account User

Authenticated SaaS user who can:

- Create and manage spaces.
- View analytics.
- Manage testimonials.
- Configure embeds.
- Manage billing.

### Public Customer

Unauthenticated visitor who can:

- Open a public testimonial collection page.
- Fill in configured fields.
- Submit a testimonial.
- Grant or deny public/social sharing consent.

---

## 4. Authentication

Provide:

- Sign up with name, email, and password.
- Login.
- Logout.
- Forgot/reset password.
- Email uniqueness validation.

New accounts default to the **Free** plan.

---

## 5. Dashboard

The authenticated dashboard should display:

### Summary Metrics

- Total spaces.
- Total testimonials.
- Total unique submitters.
- Testimonials collected during the selected period.
- Testimonials marked for Wall of Love.

### Collection Chart

Line or bar chart showing testimonials received over time.

Supported date ranges:

- Last 7 days.
- Last 30 days.
- Last 90 days.
- All time.

### Space List

Each space should show:

- Space name/title.
- Number of testimonials.
- Space limit usage.
- Public collection link.
- Shortcut to Inbox.
- Shortcut to Embed.
- Edit action.

### Free Plan Banner

Free users should see a persistent upgrade banner encouraging them to upgrade.

---

## 6. Space Management

Users can create, edit, and delete spaces.

### Space Creation Fields

Required:

- Space title.
- Subtitle.
- Testimonial prompt / “Ask”.
- Public URL slug.
- Theme.
- Whether rating is enabled.

### Configurable Submitter Fields

Default fields:

- Name — enabled and required.
- Email — enabled and required.

Optional predefined fields:

- Company name.
- Social link.
- Profile photo.

For each optional field:

- Enable/disable.
- Required/optional toggle.

### Themes

Provide at least 3 predefined themes:

- Light.
- Dark.
- Minimal/Neutral.

Themes affect the public collection page only.

### Creation Success Screen

After space creation, show:

- Success message.
- Public testimonial URL.
- Copy link button.
- Open public page button.
- Go to Inbox button.

---

## 7. Public Testimonial Collection Page

Accessible without login.

URL example:

`/s/{space-slug}`

Display:

- Space title.
- Subtitle.
- Testimonial prompt / ask.
- Configured theme.
- Configured submitter fields.
- Rating input if enabled.
- Testimonial text field.
- Consent checkbox.

### Required Testimonial Field

- Testimonial text.

### Rating

If enabled:

- 1–5 stars.
- Required for submission.

If disabled:

- Rating input is hidden.

### Consent

Display:

“I give permission for this testimonial to be displayed publicly and shared on social media.”

Store consent as a boolean.

Consent should be required before a testimonial can be publicly displayed.

### Submission

Validate all configured required fields.

On success:

- Store testimonial.
- Show a confirmation screen.
- Display a playful success message.
- Optionally display a predefined GIF/meme.

No customer account is created.

---

## 8. Testimonial Inbox

Each space has an Inbox page listing submitted testimonials.

Each testimonial card/row should display:

- Submitter name.
- Email.
- Profile photo when available.
- Company when available.
- Social link when available.
- Testimonial text.
- Rating when enabled.
- Submission date.
- Consent status.
- Visibility status.
- Favorite status.
- Wall of Love status.

### Primary Actions

#### Favorite

Star/favorite a testimonial.

Favorite testimonials should:

- Appear before non-favorites in the inbox.
- Preserve newest-first ordering within each group.

#### Wall of Love

Heart icon toggles inclusion in public/embed displays.

Only testimonials that:

- Are marked for Wall of Love.
- Are not hidden.
- Have sharing consent.

may appear in public embed widgets.

#### More Actions

Ellipsis menu:

- Rename submitter.
- Hide/unhide testimonial.
- Delete testimonial.

Delete requires confirmation.

Hidden testimonials remain in the inbox but cannot appear in public embeds.

---

## 9. Embed Builder

Each space has an Embed page.

Users can configure a testimonial widget with live preview.

### Layout Styles

Initial options:

- Masonry.
- Carousel.

### Display Options

- Dark mode on/off.
- Animation on/off.
- Background color.
- Show/hide rating.
- Show/hide submitter company.
- Show/hide profile photo.

Only eligible Wall of Love testimonials should be rendered.

### Live Preview

Changes should update the preview immediately without saving.

### Generated Embed Options

Provide:

#### JavaScript Embed

Example concept:

```html
<div data-testimonial-space="SPACE_ID"></div>
<script src="https://APP_DOMAIN/embed.js" async></script>
```

#### iframe Embed

Example concept:

```html
<iframe src="https://APP_DOMAIN/embed/SPACE_ID" width="100%" frameborder="0">
</iframe>
```

The production implementation may use signed/public configuration IDs instead of exposing internal database IDs.

### Embed Requirements

- Responsive.
- Safe to place on external websites.
- No authentication required.
- Does not expose private customer data.
- Reflects updated testimonials/configuration without requiring regenerated code.

---

## 10. Billing & Plans

## Free Plan

Price: **$0**

Limits:

- Maximum 3 spaces.
- Maximum 100 testimonials per space.

## Pro Plan

Price: **$9.99/month**

Limits:

- Maximum 25 spaces.
- Maximum 1,000 testimonials per space.

Only one paid plan is required for MVP.

> The billing UI should therefore show Free and Pro. If a three-card pricing layout is desired, the third card may be a non-purchasable “Custom / Coming Soon” placeholder, but it is not a functional plan.

### Limit Enforcement

When a user reaches a plan limit:

- Prevent creation of additional spaces.
- Prevent additional testimonial submissions once the testimonial limit for that space is reached.
- Show a clear upgrade message to the account owner.
- Public customers should receive a friendly unavailable message instead of an internal error.

---

## 11. Billing Page

Display:

- Current plan.
- Price.
- Plan limits.
- Current usage.
- Upgrade button for Free users.
- Subscription status for Pro users.
- Manage subscription button for Pro users.

### Upgrade Flow

1. User clicks Upgrade.
2. Backend creates Stripe Checkout Session.
3. User is redirected to Stripe-hosted Checkout.
4. User completes payment.
5. Stripe redirects to application success URL.
6. Application displays upgrade success message.
7. Subscription state is confirmed from Stripe/webhooks.

Never trust the redirect alone as proof of payment.

### Subscription Management

Use Stripe Customer Portal for:

- Updating payment details.
- Cancelling subscription.
- Managing subscription.

---

## 12. Stripe Integration

Required Stripe objects:

- Customer.
- Product.
- Recurring Price.
- Checkout Session.
- Subscription.

### Required Webhooks

Handle at minimum:

- `checkout.session.completed`
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.paid`
- `invoice.payment_failed`

### Billing State

Persist:

- Stripe customer ID.
- Stripe subscription ID.
- Current plan.
- Subscription status.
- Current period end.
- Cancel-at-period-end flag.

Webhook processing must be:

- Signature verified.
- Idempotent.
- Safe for duplicate delivery.

Stripe should be the source of truth for subscription status.

---

## 13. Suggested Navigation

Primary authenticated navigation:

- Dashboard
- Spaces
- Inbox
- Embed
- Billing
- Account
- Logout

Space-specific pages may use:

- Overview
- Inbox
- Embed
- Settings

---

## 14. Core Data Model

### User

- id
- name
- email
- password_hash
- plan
- stripe_customer_id
- stripe_subscription_id
- subscription_status
- subscription_period_end
- created_at
- updated_at

### Space

- id
- user_id
- title
- subtitle
- ask
- slug
- theme
- rating_enabled
- field_configuration
- created_at
- updated_at

### Testimonial

- id
- space_id
- submitter_name
- submitter_email
- company_name
- social_link
- profile_photo_url
- testimonial_text
- rating
- consent_given
- is_favorite
- is_wall_of_love
- is_hidden
- created_at
- updated_at

### EmbedConfiguration

- id
- space_id
- layout
- dark_mode
- animation_enabled
- background_color
- show_rating
- show_company
- show_profile_photo
- updated_at

### StripeWebhookEvent

- id
- stripe_event_id
- event_type
- processed_at

Used for webhook idempotency.

---

## 15. Key Business Rules

- A space belongs to exactly one user.
- Space slugs must be globally unique.
- Name and email are always enabled and required.
- Testimonial text is always required.
- Rating is required only when rating is enabled for the space.
- Optional submitter fields follow each space’s configuration.
- Public visitors never need an account.
- Favorite affects inbox ordering only.
- Wall of Love controls eligibility for public display.
- Hidden testimonials never appear publicly.
- Testimonials without sharing consent never appear publicly.
- Deleting a space deletes or archives its testimonials and embed configuration.
- Plan limits must be enforced server-side, not only in the UI.
- Billing state changes must be driven by verified Stripe events.

---

## 16. Privacy & Security Requirements

- Hash passwords using a modern password hashing algorithm.
- Protect authenticated routes.
- Enforce resource ownership server-side.
- Validate and sanitize all public form input.
- Rate-limit public testimonial submission endpoints.
- Protect public forms against spam and automated abuse.
- Validate uploaded profile photo type and size.
- Do not expose submitter email in embeds or public APIs.
- Verify Stripe webhook signatures.
- Use secure HTTP-only cookies if using cookie-based auth.
- Use HTTPS in production.

Recommended MVP anti-spam controls:

- Basic rate limiting.
- Honeypot field.
- Optional CAPTCHA integration behind a feature flag.

---

## 17. Key User Flows

### Flow A — Create Space

Sign up → Dashboard → Create Space → Configure content → Configure fields → Select theme → Enable/disable rating → Save → Success page → Copy public link.

### Flow B — Submit Testimonial

Open public link → Read prompt → Fill fields → Add testimonial → Select rating if enabled → Grant consent → Submit → Confirmation screen.

### Flow C — Manage Testimonials

Dashboard → Space → Inbox → Review testimonial → Favorite / Wall of Love / Hide / Rename / Delete.

### Flow D — Create Embed

Space → Embed → Select layout → Configure display → Preview → Copy JavaScript or iframe embed code.

### Flow E — Upgrade

Billing → Upgrade → Stripe Checkout → Payment → Redirect → Webhook updates account → Pro plan active.

---

## 18. Analytics

For MVP, calculate:

- Total testimonials.
- Unique submitter emails.
- Testimonials per day.
- Wall of Love count.
- Current space usage vs plan limit.

Analytics should support:

- 7 days.
- 30 days.
- 90 days.
- All time.

No external analytics warehouse is required for MVP.

---

## 19. Pages / Routes

### Public

- `/`
- `/signup`
- `/login`
- `/forgot-password`
- `/reset-password`
- `/s/{slug}`
- `/embed/{public-space-id}`
- `/billing/success`
- `/billing/cancel`

### Authenticated

- `/dashboard`
- `/spaces`
- `/spaces/new`
- `/spaces/{id}`
- `/spaces/{id}/edit`
- `/spaces/{id}/inbox`
- `/spaces/{id}/embed`
- `/billing`
- `/account`

---

## 20. MVP Acceptance Criteria

The MVP is complete when:

- A new user can register and log in.
- A Free account cannot create more than 3 spaces.
- A Pro account cannot create more than 25 spaces.
- Users can configure required and optional collection fields.
- Users can choose a predefined page theme.
- Users can enable or disable ratings.
- A public visitor can submit a testimonial without authentication.
- Configured required fields are enforced.
- Testimonials appear in the correct user’s inbox.
- Users can favorite, mark for Wall of Love, hide, rename, and delete testimonials.
- Favorite testimonials appear first.
- Hidden/non-consented testimonials do not appear publicly.
- Users can generate masonry and carousel embeds.
- Embed settings have a live preview.
- JavaScript and iframe embeds render correctly on an external page.
- Dashboard displays basic usage statistics and historical collection data.
- Free/Pro limits are enforced on the backend.
- Stripe Checkout upgrades a user to Pro.
- Stripe webhooks synchronize subscription status.
- Returning Pro users see their active subscription.
- Free users see an upgrade banner.
- Cancelled/expired subscriptions correctly revert to Free limits.

---

## 21. Recommended Build Order

1. Project setup, database, authentication.
2. User dashboard and navigation.
3. Space CRUD and plan-limit enforcement.
4. Public collection page and testimonial submission.
5. Testimonial inbox and management actions.
6. Dashboard analytics.
7. Wall of Love filtering.
8. Embed builder and public embed rendering.
9. Stripe Checkout, webhooks, and billing page.
10. Security, anti-spam, validation, and production hardening.

---

## 22. Implementation Guidance for AI Coding Agent

- Keep the architecture modular and MVP-focused.
- Prefer server-side enforcement for permissions, ownership, billing limits, and public visibility rules.
- Use migrations for all schema changes.
- Seed predefined themes and plan configuration.
- Store plan limits centrally rather than hard-coding them across the codebase.
- Keep Stripe logic behind a billing service/module.
- Keep embed rendering independent from the authenticated application shell.
- Create automated tests for plan limits, testimonial visibility rules, public submissions, ownership checks, and Stripe webhook idempotency.
- Do not add out-of-scope features unless required to satisfy the acceptance criteria.
