# space-management-ui Specification

## Purpose

Lets owners list, create, configure and delete their testimonial collection spaces through the web interface, with plan limits visible.

## Requirements

### Requirement: Space list

The spaces page SHALL list only the signed-in owner's spaces with title, testimonial count and links to manage, view the collection page and copy its URL.

#### Scenario: Two owners

- **WHEN** an owner opens the spaces page and another owner also has spaces
- **THEN** only the signed-in owner's spaces appear

### Requirement: Create space

Owners SHALL create a space by entering title, subtitle, ask, slug, theme, rating toggle and optional field settings, with validation errors shown inline.

#### Scenario: Valid creation

- **WHEN** an owner submits valid space details
- **THEN** the space is created and the owner is taken to its management page

#### Scenario: Duplicate slug

- **WHEN** an owner submits a slug already in use
- **THEN** the form shows a slug error and no space is created

### Requirement: Limit reached state

When the owner has reached their plan's space limit, the create control SHALL be disabled and an upgrade prompt shown, and the server MUST still refuse creation.

#### Scenario: Free owner at three spaces

- **WHEN** a Free owner with three spaces opens the spaces page or submits a new space
- **THEN** the UI shows an upgrade prompt and the submission is refused with a visible message

### Requirement: Edit space

Owners SHALL edit a space's details and field configuration, and the public identifier MUST remain unchanged.

#### Scenario: Slug change

- **WHEN** an owner changes a space's slug
- **THEN** the collection URL uses the new slug and the public identifier is unchanged

### Requirement: Delete space

Owners SHALL delete a space only after an explicit confirmation that warns its testimonials will be removed.

#### Scenario: Confirmed delete

- **WHEN** an owner confirms deletion
- **THEN** the space, its testimonials and embed configuration are removed

### Requirement: Owner-only access

Management screens for a space SHALL be accessible only to its owner.

#### Scenario: Other owner's space

- **WHEN** a user requests another owner's space management page
- **THEN** the response is forbidden or not found
