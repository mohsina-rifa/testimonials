# Spec Delta

## Purpose

Gives owners screens to review collected testimonials and control favorite, Wall of Love, hidden and deletion states safely.

## ADDED Requirements

### Requirement: Testimonial list

A space's testimonial page SHALL list its testimonials with submitter details, text, rating, consent status, and flag badges, and offer filtering by all, favorites, Wall of Love and hidden.

#### Scenario: Filter favorites

- **WHEN** the owner selects the favorites filter
- **THEN** only favorite testimonials are shown

### Requirement: Favorite and hide controls

Owners SHALL toggle favorite and hidden on each testimonial, with the new state reflected immediately.

#### Scenario: Hide testimonial

- **WHEN** the owner hides a Wall of Love testimonial
- **THEN** it is marked hidden and no longer publicly visible

### Requirement: Wall of Love consent guard

The Wall of Love control SHALL be disabled with an explanation for testimonials without consent, and the server MUST reject the change regardless.

#### Scenario: No consent

- **WHEN** an owner tries to add a non-consented testimonial to the Wall of Love
- **THEN** the control is disabled in the UI and a direct request is rejected with a visible error

### Requirement: Delete testimonial

Owners SHALL delete a testimonial only after confirmation.

#### Scenario: Confirmed delete

- **WHEN** the owner confirms deletion
- **THEN** the testimonial is removed from the list

### Requirement: Owner-only moderation

Only the owner of the space SHALL be able to view or change its testimonials.

#### Scenario: Other owner

- **WHEN** a user changes a testimonial in another owner's space
- **THEN** the request is forbidden or not found and nothing changes
