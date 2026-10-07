# plan-limits Specification

## Purpose

Defines how a user's plan is determined and how plan limits on spaces and testimonials are enforced safely, without destroying data after a downgrade.

## Requirements

### Requirement: Derived plan
A user's plan SHALL be derived from their subscription state and never stored. A user with an active subscription MUST be on Pro, and every other user MUST be on Free.

#### Scenario: No subscription
- **WHEN** a user has never subscribed
- **THEN** their plan is Free

#### Scenario: Active subscription
- **WHEN** a user has an active subscription
- **THEN** their plan is Pro

#### Scenario: Lapsed subscription
- **WHEN** a user's subscription has ended
- **THEN** their plan is Free

#### Scenario: Cancelled but in paid period
- **WHEN** a subscription is cancelled but its period has not yet ended
- **THEN** their plan is still Pro

### Requirement: Central limit definitions
Plan limits SHALL be defined in one place: Free allows 3 spaces and 100 testimonials per space, and Pro allows 25 spaces and 1,000 testimonials per space.

#### Scenario: Limits per plan
- **WHEN** limits are looked up for each plan
- **THEN** they match the values above

### Requirement: Space creation limit
Creating a space SHALL be refused when the owner already has as many spaces as their plan allows.

#### Scenario: Free user at limit
- **WHEN** a Free user with three spaces creates another
- **THEN** creation is refused and no space is added

#### Scenario: Pro user under limit
- **WHEN** a Pro user with three spaces creates another
- **THEN** the space is created

### Requirement: Testimonial submission limit
A testimonial submission SHALL be refused when the space already holds as many testimonials as its owner's plan allows, and visitors MUST see a friendly unavailable message.

#### Scenario: Full space
- **WHEN** a visitor submits to a Free owner's space holding 100 testimonials
- **THEN** the submission is refused and an unavailable message is shown

### Requirement: Concurrency-safe enforcement
The limit check and the insert SHALL occur atomically under a lock on the parent row, the user for spaces and the space for testimonials, so concurrent requests cannot exceed the limit.

#### Scenario: Concurrent creations at limit minus one
- **WHEN** two space creations race for a user one space below their limit
- **THEN** exactly one succeeds

### Requirement: Grandfathering
A user over a limit, for example after a downgrade, SHALL keep all existing spaces and testimonials. Only new creation and submissions MUST be blocked.

#### Scenario: Downgrade with five spaces
- **WHEN** a user with five spaces drops to Free
- **THEN** all five remain and creating a sixth is refused
