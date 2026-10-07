# space-management Specification

## Purpose

Defines how testimonial collection spaces are stored, identified, configured and deleted, so owners can run several isolated collection pages.

## Requirements

### Requirement: Spaces belong to one owner
Every space SHALL belong to exactly one user, and a user MAY own many spaces.

#### Scenario: Owner lists their spaces
- **WHEN** a user owns two spaces and another user owns two others
- **THEN** the user's spaces relation returns only their own two

### Requirement: Immutable public identifier
Each space SHALL receive a unique ULID public identifier at creation that MUST NOT change afterwards, regardless of slug edits.

#### Scenario: Public ID assigned on creation
- **WHEN** a space is created without an explicit public identifier
- **THEN** it has a unique ULID public identifier

#### Scenario: Slug edit keeps public ID
- **WHEN** the owner changes a space's slug
- **THEN** its public identifier is unchanged

### Requirement: Unique lowercase slug
A space's slug SHALL be unique across all spaces and MUST be lowercase and URL-safe. Titles need not be unique.

#### Scenario: Duplicate slug rejected
- **WHEN** a space is saved with a slug already used by another space
- **THEN** the save fails

#### Scenario: Duplicate title allowed
- **WHEN** two spaces share a title but have different slugs
- **THEN** both are stored

### Requirement: Theme selection
A space's theme SHALL be one of `light`, `dark` or `minimal`, defaulting to `light`.

#### Scenario: Default theme
- **WHEN** a space is created without a theme
- **THEN** its theme is `light`

### Requirement: Field configuration
A space SHALL store configuration for the optional fields `company`, `social_link` and `profile_photo`, each with `enabled` and `required` flags. A field with no stored entry MUST be treated as disabled and optional. Name and email are always enabled and required and MUST NOT be stored.

#### Scenario: Missing key means disabled
- **WHEN** a space's configuration has no entry for `social_link`
- **THEN** `social_link` is reported as disabled and optional

#### Scenario: Unknown key rejected
- **WHEN** a configuration containing a key outside the three supported fields is saved
- **THEN** the key is rejected

### Requirement: Hard deletion cascades
Deleting a space SHALL permanently delete its testimonials and embed configuration, and deleting a user SHALL permanently delete their spaces.

#### Scenario: Space deleted
- **WHEN** a space with testimonials and an embed configuration is deleted
- **THEN** no testimonial or embed configuration rows remain for it

#### Scenario: User deleted
- **WHEN** a user is deleted
- **THEN** their spaces and everything under them are deleted
