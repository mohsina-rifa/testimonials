# Spec Delta

## MODIFIED Requirements

### Requirement: Totals display

A space's dashboard SHALL show that space's total testimonials, Wall of Love count and unique submitters, for its owner only.

#### Scenario: Owner with data

- **WHEN** an owner opens the dashboard of a space with three testimonials while owning another space with more
- **THEN** the totals reflect only the opened space

### Requirement: Period filter and chart

The space dashboard SHALL let the owner choose 7, 30, 90 days or all time, and SHALL render the zero-filled daily series of that space for the chosen period.

#### Scenario: Change period

- **WHEN** the owner selects 30 days
- **THEN** the counts and chart update to the last 30 days, with zero-count days included

### Requirement: Empty state

When a space has no testimonials, its dashboard SHALL show an empty state with a prompt to share the collection page. When an owner has no spaces, the spaces page SHALL show a create-first-space call to action.

#### Scenario: New owner

- **WHEN** an owner opens the dashboard of a space with no testimonials
- **THEN** a prompt to share the collection link is shown

### Requirement: Plan usage summary

The spaces page SHALL show the owner's plan and space usage against the plan limit.

#### Scenario: Free owner with two spaces

- **WHEN** a Free owner with two spaces opens the spaces page
- **THEN** it shows "2 of 3 spaces used" on the Free plan

## ADDED Requirements

### Requirement: Space dashboard access

A space's dashboard SHALL be available only to the space's owner.

#### Scenario: Other owner

- **WHEN** a user requests the dashboard of a space they do not own
- **THEN** access is refused

## REMOVED Requirements

### Requirement: Central dashboard

**Reason**: Analytics are now per space; the owner-wide dashboard page is removed.
**Migration**: Use each space's Dashboard from the space navigation.
