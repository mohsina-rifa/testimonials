# Spec Delta

## Purpose

Defines how submitted testimonials and their submitter details are stored, normalized and removed, without any shared customer records across spaces.

## ADDED Requirements

### Requirement: Denormalized submitter data

Each testimonial SHALL store its own submitter name, email, optional company, optional social link and optional profile photo path. Submitters SHALL NOT be shared between testimonials, spaces or owners.

#### Scenario: Same person in two spaces

- **WHEN** the same email submits to two spaces
- **THEN** two independent testimonials exist, and renaming the submitter on one does not change the other

### Requirement: Normalized submitter email

A testimonial's submitter email SHALL be stored trimmed and lowercased.

#### Scenario: Mixed-case email

- **WHEN** a testimonial is saved with email ` Dana@Gmail.COM`
- **THEN** the stored email is `dana@gmail.com`

### Requirement: Optional rating

A testimonial's rating SHALL be nullable and, when present, MUST be an integer from 1 to 5. A rating MUST be required only when the space has ratings enabled.

#### Scenario: Rating out of range

- **WHEN** a rating of 6 is submitted
- **THEN** validation rejects it

#### Scenario: Ratings disabled

- **WHEN** a space has ratings disabled and a testimonial has no rating
- **THEN** the testimonial is valid

#### Scenario: Ratings turned off later

- **WHEN** ratings are disabled on a space whose testimonials already have ratings
- **THEN** the stored ratings are kept

### Requirement: Flag defaults

Consent, favorite, Wall of Love and hidden flags SHALL default to false.

#### Scenario: New testimonial

- **WHEN** a testimonial is created without flags
- **THEN** all four flags are false

### Requirement: Profile photo URL

A testimonial SHALL store only the profile photo path on the public disk, and expose a URL derived from that path, or none when no photo exists.

#### Scenario: Photo URL derived

- **WHEN** a testimonial has a stored photo path
- **THEN** its photo URL is built from the path and the public disk

#### Scenario: No photo

- **WHEN** a testimonial has no photo path
- **THEN** its photo URL is null

### Requirement: Photo removal on deletion

Deleting a testimonial, or a space or user that cascades to it, SHALL remove its profile photo file from storage.

#### Scenario: Testimonial deleted

- **WHEN** a testimonial with a stored photo is deleted
- **THEN** the file no longer exists on the public disk

#### Scenario: Space deleted

- **WHEN** a space is deleted whose testimonials have photos
- **THEN** all of those files are removed

### Requirement: Retained values for disabled fields

Disabling an optional field on a space SHALL NOT erase values already stored on testimonials.

#### Scenario: Company field disabled

- **WHEN** the company field is disabled on a space
- **THEN** existing testimonials keep their company names
