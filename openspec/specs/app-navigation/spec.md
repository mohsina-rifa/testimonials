# app-navigation Specification

## Purpose

Provides consistent navigation and a standard visual palette so every implemented screen is reachable and cohesive.

## Requirements

### Requirement: Authenticated navigation

The authenticated layout SHALL link to Dashboard, Spaces, Billing and Settings, mark the current section, and be usable on mobile.

#### Scenario: Reach all areas

- **WHEN** a signed-in user opens the navigation
- **THEN** links to Dashboard, Spaces, Billing and Settings are present

### Requirement: Space sub-navigation

A space's screens SHALL share tabs for Testimonials, Settings and Embed, with breadcrumbs back to Spaces.

#### Scenario: Switch tab

- **WHEN** an owner is on a space's testimonials and selects Embed
- **THEN** the embed screen for the same space opens

### Requirement: Standard palette

UI SHALL use a standard palette: neutral grays, one indigo primary, green for success, amber for warning and red for destructive, in light and dark modes with accessible contrast.

#### Scenario: Destructive action

- **WHEN** a delete button is displayed
- **THEN** it uses the red destructive style

### Requirement: Page states

Pages that load or change data SHALL show flash success and error messages, and lists SHALL show empty states.

#### Scenario: Flash message

- **WHEN** an action succeeds
- **THEN** a success message is displayed
