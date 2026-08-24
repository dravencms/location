# DravenCMS Location

Reusable geographical data model and administration for DravenCMS. It manages countries, regions, cities, streets, street numbers, and postal codes for packages that need structured addresses.

## Features

- Country data based on `umpirsky/country-list`.
- Region, city, street, street-number, and ZIP-code entities.
- Repositories and pair/query helpers for application forms.
- Optional administration grids and forms.
- ACL and admin-menu fixtures.

## Installation

```bash
composer require dravencms/location
```

The package loader registers its services and Doctrine attribute mappings. Apply the package schema through the application's migration workflow and load fixtures/default data before creating dependent locale or user records.

Install `dravencms/admin` to expose management presenters and `dravencms/user` to use the supplied ACL fixtures.

## Usage

Inject the repository matching the required level, such as `CountryRepository`, `CityRepository`, or `StreetRepository`. Store entity relationships rather than duplicated display strings when the address must remain queryable.

The package models location levels independently because real addresses do not always have every level. Application validation should require only the parts needed by that domain.

## License

This package is licensed under the LGPL-3.0 license.
