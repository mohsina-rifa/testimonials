# Spec Delta

## Purpose

Lets owners configure and preview how their Wall of Love looks when embedded, and exposes a public embeddable view that never leaks private data.

## ADDED Requirements

### Requirement: Embed settings form

Owners SHALL edit layout (masonry or carousel), dark mode, animation, background color, and show-rating, show-company and show-photo options for a space.

#### Scenario: Save settings

- **WHEN** an owner changes layout to carousel and saves
- **THEN** the setting is stored and shown on reload

#### Scenario: Invalid color

- **WHEN** an owner submits a malformed background color
- **THEN** an inline error is shown and nothing is saved

### Requirement: Live preview

The settings screen SHALL preview the Wall of Love using the current unsaved settings and the space's publicly visible testimonials.

#### Scenario: Toggle dark mode

- **WHEN** the owner toggles dark mode
- **THEN** the preview updates without saving

### Requirement: Embed snippet

The screen SHALL display a copyable embed snippet and public link built from the space's immutable public identifier.

#### Scenario: Slug changed

- **WHEN** the slug changes
- **THEN** the embed snippet is unchanged

### Requirement: Public embed view

The public embed view SHALL render only publicly visible testimonials using the saved settings, and MUST NOT include submitter emails.

#### Scenario: Mixed testimonials

- **WHEN** the embed view is requested for a space with hidden, non-consented and visible testimonials
- **THEN** only visible ones are rendered and no email appears in the page data

#### Scenario: Empty wall

- **WHEN** a space has no publicly visible testimonials
- **THEN** a friendly empty message is shown
