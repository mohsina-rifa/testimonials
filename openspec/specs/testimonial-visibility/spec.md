# testimonial-visibility Specification

## Purpose

Defines when a testimonial is publicly visible and guards against publishing testimonials the submitter has not consented to share.

## Requirements

### Requirement: Derived public visibility

A testimonial SHALL be publicly visible if and only if it is on the Wall of Love, is not hidden, and has consent. Visibility MUST be derived from those flags and never stored.

#### Scenario: Visible testimonial

- **WHEN** a testimonial has consent, Wall of Love set and hidden unset
- **THEN** it is publicly visible

#### Scenario: Hidden testimonial

- **WHEN** a consented Wall of Love testimonial is hidden
- **THEN** it is not publicly visible

#### Scenario: Not on the wall

- **WHEN** a consented, non-hidden testimonial is not on the Wall of Love
- **THEN** it is not publicly visible

#### Scenario: No consent

- **WHEN** a Wall of Love testimonial lacks consent
- **THEN** it is not publicly visible

### Requirement: Public visibility query

The system SHALL provide a single reusable query restriction that returns only publicly visible testimonials.

#### Scenario: Mixed testimonials

- **WHEN** a space has visible, hidden, non-consented and off-wall testimonials
- **THEN** the restriction returns only the visible ones

### Requirement: Wall of Love requires consent

The system SHALL reject turning on Wall of Love for a testimonial without consent, on the server as well as in the UI.

#### Scenario: Toggle without consent

- **WHEN** an owner enables Wall of Love on a non-consented testimonial
- **THEN** the change is rejected and the flag remains off

#### Scenario: Toggle with consent

- **WHEN** an owner enables Wall of Love on a consented testimonial
- **THEN** the flag is saved

### Requirement: Submitter email never public

Public and embed outputs SHALL NOT include a testimonial's submitter email.

#### Scenario: Public payload

- **WHEN** a publicly visible testimonial is serialized for public use
- **THEN** the output does not contain the submitter email
