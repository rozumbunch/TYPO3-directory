# Dokumentation für die Extension Directory

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
