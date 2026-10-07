# theme-toggle Specification

## Purpose
Lets signed-in users switch the interface between light and dark mode using the standard palette, with the choice remembered across visits.

## Requirements

### Requirement: Theme toggle

The authenticated layout SHALL provide a control to switch between light and dark mode, and the interface MUST apply the chosen mode immediately using the standard palette.

#### Scenario: Switch to dark

- **WHEN** a user in light mode selects dark mode from the toggle
- **THEN** the interface renders in the dark palette without a page reload

### Requirement: Theme persistence

The chosen mode SHALL persist across page loads and sessions on the same browser, and when no choice has been made the system preference MUST be used.

#### Scenario: Return visit

- **WHEN** a user who chose dark mode reloads the page
- **THEN** the page renders in dark mode without a flash of light mode
