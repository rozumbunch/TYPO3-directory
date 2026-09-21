# Site Set

The Site Set must be enabled in the site configuration:

```yaml
dependencies:
  - rozumbunch/directory
```

The Set provides:

- TypoScript for all plugins (template, partial and layout paths)
- default `persistence.storagePid` from `directory.storagePid`
- Page TSconfig (table order, record link handlers)
- Site Settings for storage and list/detail pages

The fields then appear in the backend under:

```text
Site Management > Sites > [Site] > Settings
```

```yaml
directory.storagePid: 123
directory.personListPid: 10
directory.personDetailPid: 11
directory.organisationListPid: 12
directory.organisationDetailPid: 13
```

Plugin FlexForms can override the storage folder and target pages per content element. Empty FlexForm values fall back to the Site Settings.

After changes to `settings.definitions.yaml`, the TYPO3 cache should be cleared.
