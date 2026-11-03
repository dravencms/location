# DravenCMS Location

Reusable geographical data model and administration for DravenCMS. It manages countries, regions, cities, streets, street numbers, and postal codes for packages that need structured addresses.

## Features

- Country data based on `umpirsky/country-list`.
- Region, city, street, street-number, and ZIP-code entities.
- Repositories and pair/query helpers for application forms.
- Optional administration grids and forms.
- Provider-defined ACL permissions and admin navigation.

## Installation

```bash
composer require dravencms/location
```

The package loader registers its services and Doctrine attribute mappings. Apply the package schema through the application's migration workflow and run `php bin/console location:setup` to install country data before creating dependent records.

Install `dravencms/admin` to expose management presenters and `dravencms/user` for provider-defined permissions.

## Usage

Inject the repository matching the required level, such as `CountryRepository`, `CityRepository`, or `StreetRepository`. Store entity relationships rather than duplicated display strings when the address must remain queryable.

The package models location levels independently because real addresses do not always have every level. Application validation should require only the parts needed by that domain.

## License

This package is licensed under the LGPL-3.0 license.

## Installing default data

Run `php bin/console location:setup` after creating or migrating the application schema. The command adds missing defaults in a transaction and can be rerun on an active project. It never purges data or updates existing records, including customized names, formats, templates, active flags and default selections. Run setup commands one at a time. Schema creation/migration remains separate. Fixtures are no longer registered.

Adds all countries from the installed English country dataset, matching by country code. Add `--example-address` to explicitly install the former Olomouc / 77900 / Brněnská / 54 example; address rows are matched within their parent country, city, ZIP code or street.
