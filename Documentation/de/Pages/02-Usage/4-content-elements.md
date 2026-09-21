# Inhaltselemente

Die Plugins erscheinen im New-Content-Wizard in der Gruppe **Directory**. Jede Instanz wird im FlexForm konfiguriert.

| Plugin | CType | Zweck |
|---|---|---|
| Personenliste | `directory_personlist` | Liste von Personen |
| Personendetail | `directory_persondetail` | Einzelne Person (Routing oder gewählter Datensatz) |
| Organisationsliste | `directory_organisationlist` | Liste von Organisationen |
| Organisationsdetail | `directory_organisationshow` | Einzelne Organisation |
| Organisations-Organigramm | `directory_organisationdetail` | Organigramm mit Abschnitten Struktur / Standorte / Kontakte |
| Standortkarte | `directory_map` | Interaktive Karte, siehe [Standortkarte](5-map.md) |

## Personenliste

Sheets: **Darstellung**, **Quelle**, **Filter**.

- **Anzeigemodus**: `compact`, `photo`, `full`
- **Layout**: `row`, `block-2`, `block-3`
- **Storage-Ordner** und **Detailseite**: optional, Fallback auf Site Settings
- **Filtermodus `frontend`**: Besuchende können nach Name, Organisation, PLZ und Ort suchen
- **Filtermodus `backend`**: Redaktion setzt Suche, Organisation, PLZ und Ort fest; kein Frontend-Formular

Leere Felder werden nicht gerendert.

## Organisationsliste

Gleiches Anzeige-/Layout-Muster (`compact`, `logo`, `full`). Zusätzlich:

- **Organisationstypen**: Standard sind die Root-Typen (`company`, `authority`, `association`, `institution`, `other`)
- Frontend-Filter: Suche, Typ, Adresse (PLZ/Ort)

## Personendetail / Organisationsdetail

Plugin auf einer eigenen Seite platzieren. Der Datensatz kommt aus der Slug-Route oder aus dem Request. Im FlexForm können Storage-Ordner, Listenseite und (bei Organisationen) die Personendetailseite gesetzt werden.

SEO: `seo_title` / `seo_description` mit Fallback auf Name / Kurzbeschreibung.

## Organisations-Organigramm

Zeigt eine Organisation (Auswahl im FlexForm) mit den Abschnitten:

- `structure`
- `locations`
- `contacts`

## Route Enhancer

Import von `EXT:directory/Configuration/Sets/Directory/route-enhancers.yaml`. Beispielpfade:

- Deutsch: `/profil/{slug}`, `/organisation/{slug}`
- Englisch: `/profile/{slug}`, `/organization/{slug}`

Organigramm-Abschnitte: `/structure`, `/locations`, `/contacts`.
