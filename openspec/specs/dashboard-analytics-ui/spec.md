# dashboard-analytics-ui Specification

## Purpose

Shows an owner their testimonial analytics and plan usage on the dashboard, using the existing analytics computations.

## Requirements

### Requirement: Totals display

The dashboard SHALL show total spaces, total testimonials, Wall of Love count and unique submitters for the signed-in owner only.

#### Scenario: Owner with data

- **WHEN** an owner with two spaces and three testimonials opens the dashboard
- **THEN** the totals reflect only that owner's data

### Requirement: Period filter and chart

The dashboard SHALL let the owner choose 7, 30, 90 days or all time, and SHALL render the zero-filled daily series for the chosen period.

#### Scenario: Change period

- **WHEN** the owner selects 30 days
- **THEN** the counts and chart update to the last 30 days, with zero-count days included

### Requirement: Empty state

When an owner has no spaces, the dashboard SHALL show an empty state with a prompt to create a first space.

#### Scenario: New owner

- **WHEN** a user with no spaces opens the dashboard
- **THEN** a create-space call to action is shown

### Requirement: Plan usage summary

The dashboard SHALL show the owner's plan and space usage against the plan limit.

#### Scenario: Free owner with two spaces

- **WHEN** a Free owner with two spaces opens the dashboard
- **THEN** it shows "2 of 3 spaces used" on the Free plan
