# Standortkarte

Das Plugin **Standortkarte** rendert eine interaktive Leaflet-Karte (OpenStreetMap-Kacheln) plus eine optionale barrierefreie Marker-Liste.

Nur Datensätze mit Koordinaten werden zu Markern. Datensätze ohne Breite/Länge bleiben in Listen, nicht auf der Karte.

## Marker-Arten

Im FlexForm mindestens eine Art wählen:

| Art | Quelle |
|---|---|
| `locations` | Standortdatensätze |
| `organisations` | Standorte von Organisationen |
| `contacts` | Standorte der Ansprechpartner |
| `persons` | Private Standorte ausgewählter Personen |

Optionale Filter: Standorttypen (`company`, `poi`, `other`, `private`), ausgewählte Organisationen, Untereinheiten einbeziehen, ausgewählte Personen.

## Darstellung

- **Höhe**: 360 / 480 / 640 / 800 px
- **Legende**: Arten als Kartenlegende
- **Liste**: barrierefreie Alternative zur Karte
- **Consent**: Kacheln laden erst nach Einwilligung (Standard an)

Detail-Links nutzen `directory.organisationDetailPid` und `directory.personDetailPid` (oder FlexForm-Overrides). Popups enthalten einen Link zu einem externen Routenplaner.

Das bestehende SVG-Karten-Inhaltselement im Sitepackage ist unabhängig von diesem Plugin.
