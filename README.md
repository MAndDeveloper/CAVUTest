# CAVU TECH TEST
Customers should be able to check if there’s an available car parking space for a given date range.
- Customer should be able to check parking price for the given dates (i.e.,
Weekday/Weekend Pricing, Summer/Winter Pricing)
- Customers should be able to create a booking for given dates (from - to)
- Customers should be able to cancel their booking.
- Customers should be able to amend their booking

## Scope

Simple migration to create parked table (list of cars parked/booked)
Model for a "Space" to be created with base conditions;
Lisence Plate
Start date
End date
Pricing
Model for Pricing;
Weekend/day/season.

Simple API set;
GET price
GET availibility
POST booking
PUT modification
DELETE cancelation

## Considerations on Constraints

10 spaces maximum, check for row count on booking, if 10 are present for date set, return filled response, 200 still to be used as API has not failed.

If less than 10 are present, count number below.
Date formatting and checking should be simple with basic Carbon usage.

Carbon::betweenIncluded will be vital.

## Notes on Solutions

Assumed inputs will be uniform JSON objects. For instance a DELETE request to /parking would be formatted as

```
{
  "registration": "XX55 X55",
  "arrival": 05.10.2024
}

```

### Final notes
Please ignore present dockerfiles, empty repo was copied from previous application.

Included work;

2x Migrations

2x Models

1x Controller

1x Routing File