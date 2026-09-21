# Site Set

Das Site Set muss in der Site-Konfiguration aktiviert sein:

```yaml
dependencies:
  - rozumbunch/directory
```

Das Set stellt bereit:

- TypoScript für alle Plugins (Template-, Partial- und Layout-Pfade)
- Standard-`persistence.storagePid` aus `directory.storagePid`
- Page TSconfig (Tabellenreihenfolge, Record-Link-Handler)
- Site Settings für Storage sowie Listen- und Detailseiten

Danach erscheinen die Felder im Backend unter:

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

Plugin-FlexForms können Storage-Ordner und Zielseiten pro Inhaltselement überschreiben. Leere FlexForm-Werte fallen auf die Site Settings zurück.

Bei Änderungen an `settings.definitions.yaml` sollte der TYPO3-Cache geleert werden.
