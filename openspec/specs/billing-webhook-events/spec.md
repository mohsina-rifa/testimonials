# billing-webhook-events Specification

## Purpose

Defines how billing state and processed Stripe webhook events are recorded so subscription handling is reliable and idempotent.

## Requirements

### Requirement: Subscription state storage
Billing state SHALL be stored in the subscription structures provided by the billing library, and no plan field SHALL exist on users.

#### Scenario: User without plan column
- **WHEN** the users table is inspected
- **THEN** it has billing identifier columns but no plan column

### Requirement: Webhook event recording
The system SHALL record each processed Stripe webhook event with its Stripe event ID, event type and processing time.

#### Scenario: Event processed
- **WHEN** a webhook event is processed
- **THEN** a record with its ID, type and timestamp exists

### Requirement: Event idempotency
Each Stripe event ID SHALL be recorded at most once, so a redelivered event can be detected and skipped.

#### Scenario: Duplicate event ID
- **WHEN** an event ID that is already recorded is recorded again
- **THEN** the second record is rejected
