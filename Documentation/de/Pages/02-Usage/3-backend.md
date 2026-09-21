# Backend

## Umgesetzt

- Personen, Organisationen und Standorte anlegen und bearbeiten
- Private Adressen an der Person (Side-by-Side, Typ `private`)
- Standorte an der Organisation (Side-by-Side, nicht privat)
- Organisationen an der Person (nur Auswahl)
- Ansprechpartner an der Organisation (nur Auswahl)
- Geschäftsbereiche: Abteilung und Organisationseinheit
- FAL: Profilbild/Logo und Medien
- Sortieren, verbergen, Start/Stop, Sprache, Workspace
- Formulare mit Tabs
- Suche über `ctrl.searchFields`
- Datensatzlabels inkl. lokalisierter Typzusätze
- Slug `uniqueInSite`
- Kontakt als TCA `type=link`
- Koordinaten: manuell, Validierung (Eval + Hook), leer = NULL
- Record-Link-Handler für Person und Organisation (RTE / Link-Wizard)

## Koordinaten

Breiten- und Längengrad dürfen leer bleiben. Ungültige Werte werden abgelehnt. Leere Koordinaten speichern `NULL`. Solche Datensätze bleiben in Listen sichtbar, erzeugen aber keinen Kartenmarker.
