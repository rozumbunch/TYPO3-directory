[![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-13.4-ff8700?maxAge=3600&logo=typo3)](https://get.typo3.org/)
[![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-14.0-ff8700?maxAge=3600&logo=typo3)](https://get.typo3.org/)

# TYPO3 extension `directory`

Directory of persons, organisations and locations for TYPO3.

|                    | URL                                                                          |
|--------------------|------------------------------------------------------------------------------|
| **Read Extension Manual:** | [English](Documentation/base/index.md) · [Deutsch](Documentation/de/index.md) |

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

## Installation

```bash
composer req rozumbunch/directory
```

Usage, Site Set, plugins and the map are documented in the extension manual:

- [English](Documentation/base/index.md)
- [Deutsch](Documentation/de/index.md)

In the TYPO3 backend the same documentation is available via **Help > Documentation** when [documentationhub](https://github.com/rozumbunch/TYPO3-documentation) is installed.

## Requirements

- TYPO3 13.4+ / 14.0+
- PHP 8.2+

## Feedback and Support

You can reach us on the [Contact Form](https://www.rozumbunch.com/kontakt)

---

## Deutsch

[← English version](#typo3-extension-directory)

# TYPO3-Extension `directory`

## Funktionen

### Zentrale Datenmodelle

Die Extension basiert auf drei miteinander verknüpften Kernmodellen:

- Personen
- Organisationen
- Standorte

Damit lassen sich persönliche Daten und Kontaktdaten ebenso verwalten wie unterschiedliche Arten von Organisationen, Unternehmen und Einrichtungen. Organisationen und Personen können mit Adressen beziehungsweise Standorten verknüpft werden.

Die Standortdaten können außerdem als POI-Verzeichnis (Points of Interest) ausgegeben werden.

Durch die typische TYPO3-Ordnerstruktur lassen sich Datensätze nach unterschiedlichen Bereichen und Verwendungszwecken organisieren. Die Darstellung im Frontend wird über flexibel konfigurierbare Plugins gesteuert.

### Plugins und Inhaltselemente

Die Extension stellt folgende Inhaltselemente bereit:

- Personenliste und Personendetailansicht
- Organisationsliste und Organisationsdetailansicht
- Unternehmensorganigramm
- Interaktive Standortkarte

### Filtermöglichkeiten

Datensätze können sowohl über die Plugin-Konfiguration als auch über Filter im Frontend eingeschränkt und gezielt ausgegeben werden.

### Site Set

Das Site Set `rozumbunch/directory` enthält zentrale Site Settings, unter anderem für:

- Storage-Ordner
- Detailseiten
- weitere grundlegende Einstellungen der Extension

### Suchmaschinenfreundliche URLs

Ein Route Enhancer ermöglicht sprachspezifische und lesbare Detailseiten-URLs auf Basis von Slugs.

### Kartenansicht

Standorte werden mithilfe von Leaflet und OpenStreetMap auf einer interaktiven Karte dargestellt.

Zusätzlich unterstützt die Kartenansicht:

- eine optionale Consent-Abfrage vor dem Laden der Karte
- eine barrierefreie Listenansicht der dargestellten Standorte

### Beispieldaten

Für Entwicklungs- und Testzwecke können Dummy-Daten über folgenden Befehl angelegt werden:

```bash
vendor/bin/typo3 directory:seed-dummy
```

## Installation

```bash
composer req rozumbunch/directory
```

Nutzung, Site Set, Plugins und Karte stehen im Extension-Manual:

- [Deutsch](Documentation/de/index.md)
- [English](Documentation/base/index.md)

Im TYPO3-Backend ist dieselbe Dokumentation über **Help > Documentation** erreichbar, wenn [documentationhub](https://github.com/rozumbunch/TYPO3-documentation) installiert ist.

## Voraussetzungen

- TYPO3 13.4+ / 14.0+
- PHP 8.2+

## Feedback und Support

Erreichbar über das [Kontaktformular](https://www.rozumbunch.com/kontakt)
