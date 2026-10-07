# testimonial-collection Specification

## Purpose

Lets visitors submit testimonials on a public per-space page whose form reflects the space's configuration, without needing an account.

## Requirements

### Requirement: Public collection page

Each space SHALL have a public page at its slug showing its title, subtitle, ask and theme, accessible without authentication.

#### Scenario: Visitor opens page

- **WHEN** an anonymous visitor opens a space's collection URL
- **THEN** the title, subtitle, ask and form are shown in the space's theme

#### Scenario: Unknown slug

- **WHEN** a visitor opens a slug that does not exist
- **THEN** a not-found page is shown

### Requirement: Configured form fields

The form SHALL always include name, email, testimonial text and consent, and SHALL include company, social link, profile photo and rating only when enabled, marking required ones.

#### Scenario: Company enabled and required

- **WHEN** a space enables and requires company
- **THEN** the form shows a required company field and rejects submissions without it

#### Scenario: Ratings disabled

- **WHEN** a space has ratings disabled
- **THEN** no rating input is shown

### Requirement: Submission outcome

A successful submission SHALL store the testimonial and show a thank-you state; validation failures MUST show inline errors and keep entered values.

#### Scenario: Success

- **WHEN** a visitor submits valid details
- **THEN** the testimonial is stored and a thank-you message replaces the form

### Requirement: Full space unavailable

When the space has reached its owner's testimonial limit, the page SHALL show a friendly unavailable message instead of the form and submissions MUST be refused.

#### Scenario: Free space with 100 testimonials

- **WHEN** a visitor opens or submits to a full space
- **THEN** a friendly unavailable message is shown and nothing is stored
