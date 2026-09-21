# Installation und Konfiguration

1. Extension per Composer installieren

```bash
composer req rozumbunch/directory
```

2. Site Set in der Site-Konfiguration aktivieren

```yaml
dependencies:
  - rozumbunch/directory
```

3. Site Settings eintragen

Unter **Site Management > Sites > [Site] > Settings** setzen:

| Setting | Zweck |
|---|---|
| `directory.storagePid` | SysOrdner für Datensätze und Standard-Listenquelle |
| `directory.personListPid` | Seite mit dem Personenlisten-Plugin |
| `directory.personDetailPid` | Seite mit dem Personendetail-Plugin |
| `directory.organisationListPid` | Seite mit dem Organisationslisten-Plugin |
| `directory.organisationDetailPid` | Seite mit dem Organisationsdetail-Plugin |

4. Route Enhancer importieren (optional, für Slug-URLs)

```yaml
imports:
  -
    resource: 'EXT:directory/Configuration/Sets/Directory/route-enhancers.yaml'
```

5. Datensätze anlegen und Inhaltselemente platzieren

Storage-Ordner öffnen, Organisationen, Standorte und Personen anlegen, danach **Personenliste**, **Organisationsliste**, **Personendetail**, **Organisationsdetail**, **Organisations-Organigramm** oder **Standortkarte** auf einer Seite einfügen.

## Dummy-Daten

Für die lokale Entwicklung:

```bash
vendor/bin/typo3 directory:seed-dummy
```

Optionen:

- `--pid=123`: Storage-Ordner (Standard: `directory.storagePid`)
- `--replace`: vorhandene Dummy-Datensätze (`identifier` beginnt mit `dummy-`) zuerst löschen
- `--wipe`: alle Directory-Datensätze auf dieser PID löschen, dann neu anlegen
