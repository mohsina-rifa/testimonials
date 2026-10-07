# Spec Delta

## Purpose

Presents the product to anonymous visitors on the home page, replacing the default Laravel welcome page, and directs them to sign up or log in.

## ADDED Requirements

### Requirement: Public home page

The root URL SHALL render a product marketing page with a hero, a feature overview and a call to action, and MUST NOT render the default Laravel welcome content.

#### Scenario: Anonymous visitor opens root

- **WHEN** an unauthenticated visitor opens `/`
- **THEN** the page shows the product name, feature sections and links to register and log in

#### Scenario: Authenticated visitor opens root

- **WHEN** a signed-in user opens `/`
- **THEN** the header offers a link to the dashboard instead of register and log in

### Requirement: Pricing presentation

The home page SHALL display Free and Pro plans with their space and per-space testimonial limits, taken from the central plan limits.

#### Scenario: Limits shown

- **WHEN** the home page is displayed
- **THEN** Free shows 3 spaces and 100 testimonials per space, and Pro shows 25 spaces and 1,000 testimonials per space

### Requirement: Authentication entry points

Login, registration, password reset and email verification screens SHALL share the product branding and link to one another.

#### Scenario: Login screen links

- **WHEN** the login screen is displayed
- **THEN** it links to registration and password reset and shows the product name
