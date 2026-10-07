# Spec Delta

## Purpose

Shows owners their plan, usage and limits, and lets them upgrade or manage their subscription, with clear states when limits are reached.

## ADDED Requirements

### Requirement: Billing page

A billing page SHALL show the owner's current plan, space usage and the highest per-space testimonial usage against plan limits.

#### Scenario: Free owner

- **WHEN** a Free owner opens the billing page
- **THEN** the Free plan, its limits and current usage are shown with an upgrade action

### Requirement: Upgrade flow

A Free owner SHALL be able to start Stripe Checkout for Pro from the billing page and from limit prompts.

#### Scenario: Start checkout

- **WHEN** a Free owner chooses Upgrade
- **THEN** they are redirected to Stripe Checkout

#### Scenario: Billing not configured

- **WHEN** Stripe keys or the Pro price are not configured
- **THEN** the page shows a notice that billing is unavailable and the upgrade action is disabled

### Requirement: Manage subscription

A Pro owner SHALL see a link to the Stripe billing portal, and a cancelled owner within the paid period SHALL see the end date and remain shown as Pro.

#### Scenario: Cancelled in paid period

- **WHEN** a subscription is cancelled but not yet ended
- **THEN** the page shows Pro with the access end date

### Requirement: Over-limit after downgrade

After a downgrade, an owner above Free limits SHALL see a notice that existing data is kept but new spaces or testimonials are blocked until under the limit.

#### Scenario: Four spaces on Free

- **WHEN** a lapsed owner with four spaces opens the billing page
- **THEN** a notice states data is kept and new spaces cannot be created
