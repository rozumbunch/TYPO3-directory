# Location map

The **Location map** plugin renders an interactive Leaflet map (OpenStreetMap tiles) plus an optional accessible list of markers.

Only records with coordinates become markers. Records without latitude/longitude stay in lists, not on the map.

## Marker kinds

Select at least one kind in the FlexForm:

| Kind | Source |
|---|---|
| `locations` | Location records |
| `organisations` | Organisation locations |
| `contacts` | Locations of organisation contact persons |
| `persons` | Private locations of selected persons |

Optional filters: location types (`company`, `poi`, `other`, `private`), selected organisations, include sub-units, selected persons.

## Display

- **Height**: 360 / 480 / 640 / 800 px
- **Legend**: kinds as a map legend
- **List**: accessible alternative to the map
- **Require consent**: tiles load only after consent (default on)

Detail links use `directory.organisationDetailPid` and `directory.personDetailPid` (or FlexForm overrides). Popups include a link to an external route planner.

The existing SVG map content element in the sitepackage is independent of this plugin.
