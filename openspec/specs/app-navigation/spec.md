# app-navigation Specification

## Purpose

Provides consistent navigation and a standard visual palette so every implemented screen is reachable and cohesive.

## Requirements

### Requirement: Authenticated navigation

The authenticated layout SHALL link to Spaces, Billing and Settings, mark the current section, be usable on mobile, show no starter-kit default links, and display the application name from configuration as its brand. There MUST be no global Dashboard link.

#### Scenario: Reach all areas

- **WHEN** a signed-in user opens the navigation
- **THEN** links to Spaces, Billing and Settings are present and no Dashboard or starter-kit links are shown

#### Scenario: Branding

- **WHEN** the application name is configured as "Testimonials"
- **THEN** the navigation brand and page titles show "Testimonials" rather than the starter-kit name

### Requirement: Space sub-navigation

When a space is selected, the navigation SHALL show Dashboard, Inbox, Embed and Space (the edit page) entries for that space, mark the current one, and provide a way back to Spaces.

#### Scenario: Switch tab

- **WHEN** an owner is on a space's Inbox and selects Embed
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

### Requirement: Space switcher

The authenticated layout SHALL let an owner switch between their own spaces, keeping the current section when possible, and MUST NOT list other owners' spaces.

#### Scenario: Switch space

- **WHEN** an owner with spaces A and B is viewing A's Embed and selects B
- **THEN** B's Embed screen opens

### Requirement: Post-authentication landing

After login, registration or email verification, the user SHALL be taken to the spaces list, and no central dashboard page SHALL exist.

#### Scenario: Login

- **WHEN** a user logs in successfully
- **THEN** they land on the spaces page

#### Scenario: Old dashboard URL

- **WHEN** a user requests `/dashboard`
- **THEN** the response is not found
