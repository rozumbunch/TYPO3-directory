# Backend

## Implemented

- Create and edit persons, organisations and locations
- Private addresses on the person (side-by-side, type `private`)
- Locations on the organisation (side-by-side, not private)
- Organisations on the person (selection only)
- Contact persons on the organisation (selection only)
- Business units: department and organisational unit
- FAL: portrait/logo and media
- Sorting, hide, start/stop, language, workspace
- Tabbed forms
- Search via `ctrl.searchFields`
- Record labels including localised type suffixes
- Slug `uniqueInSite`
- Contact fields as TCA `type=link`
- Coordinates: manual input, validation (eval + hook), empty = NULL
- Record link handlers for person and organisation (RTE / link wizard)

## Coordinates

Latitude and longitude can stay empty. Invalid values are rejected. Empty coordinates store `NULL`. Such records remain visible in lists but produce no map marker.
