# Documentation for Extension Directory

## Features

### Core data models

The extension is based on three related core models:

- Persons
- Organisations
- Locations

This covers personal and contact data as well as different kinds of organisations, companies and institutions. Organisations and persons can be linked to addresses or locations.

Location data can also be published as a POI directory (points of interest).

Typical TYPO3 folder structures can be used to organise records by area and purpose. Frontend output is controlled by flexibly configurable plugins.

### Plugins and content elements

The extension provides the following content elements:

- Person list and person detail view
- Organisation list and organisation detail view
- Company organigram
- Interactive location map

### Filtering

Records can be limited and targeted both through plugin configuration and through frontend filters.

### Site Set

The Site Set `rozumbunch/directory` provides central Site Settings, including:

- Storage folder
- Detail pages
- further basic extension settings

### Search-engine-friendly URLs

A route enhancer provides language-specific, readable detail URLs based on slugs.

### Map view

Locations are shown on an interactive map using Leaflet and OpenStreetMap.

The map view also supports:

- an optional consent prompt before the map is loaded
- an accessible list of the displayed locations

### Sample data

Dummy data for development and testing can be created with:

```bash
vendor/bin/typo3 directory:seed-dummy
```
