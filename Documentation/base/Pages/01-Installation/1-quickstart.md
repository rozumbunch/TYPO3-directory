# Install and Configuration

1. Install the extension via Composer

```bash
composer req rozumbunch/directory
```

2. Enable the Site Set in the site configuration

```yaml
dependencies:
  - rozumbunch/directory
```

3. Enter Site Settings

Go to **Site Management > Sites > [Site] > Settings** and set:

| Setting | Purpose |
|---|---|
| `directory.storagePid` | SysFolder for records and default list source |
| `directory.personListPid` | Page with the person list plugin |
| `directory.personDetailPid` | Page with the person detail plugin |
| `directory.organisationListPid` | Page with the organisation list plugin |
| `directory.organisationDetailPid` | Page with the organisation detail plugin |

4. Import route enhancers (optional, for slug URLs)

```yaml
imports:
  -
    resource: 'EXT:directory/Configuration/Sets/Directory/route-enhancers.yaml'
```

5. Create records and add content elements

Open the Directory storage folder, create organisations, locations and persons, then place **Person list**, **Organisation list**, **Person detail**, **Organisation detail**, **Organisation organigram** or **Location map** on a page.

## Dummy data

For local development:

```bash
vendor/bin/typo3 directory:seed-dummy
```

Options:

- `--pid=123`: storage folder (default: `directory.storagePid`)
- `--replace`: delete existing dummy records (`identifier` starting with `dummy-`) first
- `--wipe`: delete all Directory records on that PID, then seed again
