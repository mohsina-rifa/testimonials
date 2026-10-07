# Testimonials

A testimonial-collection SaaS. Users own **Spaces**, and each Space has:

- a public collection form (`/s/{slug}`)
- moderation tools (favorite, hide, show on the Wall of Love, delete)
- an analytics dashboard
- an embeddable Wall of Love (`/embed/{publicId}`)

Plans (Free and Pro) are billed through Stripe. Plan limits live in `config/plans.php`.

**Repository:** https://github.com/mohsina-rifa/testimonials

## Stack

Laravel 13 on PHP 8.4, Inertia v3 with React 19 and TypeScript, Tailwind 4, Cashier (Stripe), Fortify (auth, 2FA, passkeys), Wayfinder, Pest 5. The frontend toolchain is Vite+ (`vp`).

## How to run the project

Requirements: PHP 8.4, Composer, Node.js, and MySQL.

```bash
git clone https://github.com/mohsina-rifa/testimonials.git
cd testimonials
composer setup   # install deps, create .env, generate key, migrate, build assets
composer dev     # start the dev stack
```

Set your MySQL credentials in `.env` before `composer setup` if the defaults don't match your machine.

### Stripe (optional)

To enable billing, set these in `.env`:

```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRO_PRICE_ID=
```

Without them, the "Upgrade to Pro" button stays disabled.

### Commands

| Command                               | Purpose                                  |
| ------------------------------------- | ---------------------------------------- |
| `composer dev`                        | Run the dev stack                        |
| `composer test`                       | Pint check, PHPStan, then the test suite |
| `composer types:check`                | PHPStan                                  |
| `npm run check` / `npm run check:fix` | Lint and format the frontend             |
| `npm run types:check`                 | TypeScript check                         |
| `npm run build`                       | Build assets                             |

Tests run against MySQL (`testimonials_testing`, configured in `phpunit.xml`). CI (`.github/workflows/tests.yml`) runs the same checks on push and pull requests.

## Feature status

Based on the PRD in [`docs/`](docs) and the specs in [`openspec/specs/`](openspec/specs).

### Done

- Auth: register, login, password reset, email verification, 2FA, passkeys (Fortify)
- Spaces: create, edit, delete, with configurable submitter fields and a theme
- Public collection page (`/s/{slug}`) with star rating and consent
- Testimonial inbox and moderation: favorite, Wall of Love, hide, delete, filters
- Analytics dashboard with 7 / 30 / 90 day and all-time ranges
- Embed builder: layout, display options, `/embed/{publicId}` wall, iframe snippet
- Plans (Free and Pro) with limit enforcement from `config/plans.php`
- Billing page with Stripe Checkout upgrade and billing portal link
- Space-centric navigation, light/dark theme toggle, landing page
- Feature and unit tests for the above

### Not done or unverified

- JavaScript embed snippet: only the iframe snippet is generated
- Stripe webhook sync: events are stored (`StripeWebhookEvent`), but I haven't verified the full subscription-sync flow against live Stripe
- Everything listed under the PRD's non-goals: video testimonials, custom domains, teams, custom CSS, API access, multiple paid tiers, AI-generated content, third-party imports
- Production deployment

## Agentic tools used

| Tool                       | Where                                                                                                                                                                                                                                                  |
| -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Claude Code                | Main coding agent. Project rules are in [`CLAUDE.md`](CLAUDE.md).                                                                                                                                                                                      |
| Laravel Boost (MCP server) | [`.mcp.json`](.mcp.json) runs `php artisan boost:mcp`. It gives the agent schema, logs, docs search and URL tools. Config is in [`boost.json`](boost.json).                                                                                            |
| Agent guidelines           | [`AGENTS.md`](AGENTS.md) holds the Laravel Boost guidelines.                                                                                                                                                                                           |
| Skills                     | [`.claude/skills/`](.claude/skills) and [`.agents/skills/`](.agents/skills): Cashier, Fortify, Inertia React, Tailwind, Wayfinder, Laravel and testing best practices, deploying to Cloud, and the OpenSpec skills.                                    |
| OpenSpec workflow          | Slash commands in [`.claude/commands/opsx/`](.claude/commands/opsx) (`/opsx:propose`, `apply`, `archive`, and more). Specs are in [`openspec/specs/`](openspec/specs) and finished changes in [`openspec/changes/archive/`](openspec/changes/archive). |
| Codex and Gemini config    | [`.codex/`](.codex) and [`.gemini/`](.gemini), generated by Boost.                                                                                                                                                                                     |

No custom subagents or hooks are checked into this repo.

## Documentation

- [`docs/`](docs): product requirements and data model
- [`openspec/specs/`](openspec/specs): capability specs
