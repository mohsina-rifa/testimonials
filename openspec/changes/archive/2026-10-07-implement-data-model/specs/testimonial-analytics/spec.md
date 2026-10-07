# Spec Delta

## Purpose

Defines the analytics an owner sees across their spaces, computed on demand from stored testimonials so figures can never drift.

## ADDED Requirements

### Requirement: Owner totals
The system SHALL report an owner's total spaces, total testimonials across their spaces, and Wall of Love testimonial count.

#### Scenario: Two spaces
- **WHEN** an owner has two spaces with three testimonials, two of them on the Wall of Love
- **THEN** totals are 2 spaces, 3 testimonials and 2 on the Wall of Love

### Requirement: Unique submitters
The system SHALL count unique submitters as distinct submitter emails across the owner's spaces only.

#### Scenario: Same person in two of the owner's spaces
- **WHEN** one email submits to two of the owner's spaces
- **THEN** it counts once

#### Scenario: Other owners excluded
- **WHEN** the same email also submitted to another owner's space
- **THEN** that submission does not affect this owner's count

### Requirement: Period filter
Collected-testimonial counts SHALL be filterable to the last 7, 30 or 90 days, or all time.

#### Scenario: 7-day window
- **WHEN** one testimonial is 3 days old and another is 40 days old
- **THEN** the 7-day count is 1 and the all-time count is 2

### Requirement: Zero-filled daily series
The daily chart series SHALL contain one entry per day in the period, with zero for days without testimonials, bucketed by the application timezone.

#### Scenario: Gap days
- **WHEN** testimonials exist on day 1 and day 3 of a 3-day period
- **THEN** the series has three entries and day 2 is zero

### Requirement: Live computation
Analytics SHALL be computed from stored testimonials at request time, with no separately stored aggregates.

#### Scenario: New testimonial
- **WHEN** a testimonial is added
- **THEN** the next analytics read includes it
