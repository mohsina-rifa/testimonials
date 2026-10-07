# embed-configuration Specification

## Purpose

Defines the per-space settings that control how a space's Wall of Love appears when embedded, with defaults that always exist.

## Requirements

### Requirement: One configuration per space

Each space SHALL have exactly one embed configuration.

#### Scenario: Second configuration rejected

- **WHEN** a second embed configuration is saved for the same space
- **THEN** the save fails

### Requirement: Created with defaults

Creating a space SHALL also create its embed configuration with layout `masonry`, dark mode off, animation on, background `#ffffff`, and rating, company and profile photo all shown.

#### Scenario: New space

- **WHEN** a space is created
- **THEN** its embed configuration exists with the default values

### Requirement: Supported layouts

The embed layout SHALL be either `masonry` or `carousel`.

#### Scenario: Invalid layout

- **WHEN** a layout other than `masonry` or `carousel` is saved
- **THEN** it is rejected

### Requirement: Configuration removed with space

Deleting a space SHALL delete its embed configuration.

#### Scenario: Space deleted

- **WHEN** a space is deleted
- **THEN** its embed configuration no longer exists
