# Content elements

Plugins appear in the New Content wizard under the group **Directory**. Configure each instance in the FlexForm.

| Plugin | CType | Purpose |
|---|---|---|
| Person list | `directory_personlist` | List of persons |
| Person detail | `directory_persondetail` | Single person (routing or selected record) |
| Organisation list | `directory_organisationlist` | List of organisations |
| Organisation detail | `directory_organisationshow` | Single organisation |
| Organisation organigram | `directory_organisationdetail` | Organigram with sections structure / locations / contacts |
| Location map | `directory_map` | Interactive map, see [Location map](5-map.md) |

## Person list

Sheets: **Display**, **Source**, **Filter**.

- **Display mode**: `compact`, `photo`, `full`
- **Layout**: `row`, `block-2`, `block-3`
- **Storage folder** and **detail page**: optional, fallback to Site Settings
- **Filter mode `frontend`**: visitors can search by name, organisation, ZIP and city
- **Filter mode `backend`**: editors fix search, organisation, ZIP and city; no frontend form

Empty fields are not rendered.

## Organisation list

Same display/layout pattern (`compact`, `logo`, `full`). Additional options:

- **Organisation types**: default root types (`company`, `authority`, `association`, `institution`, `other`)
- Frontend filters: search, type, address (ZIP/city)

## Person detail / Organisation detail

Place the plugin on a dedicated page. The record comes from the slug route or from the request. FlexForm can set storage folder, list page and (for organisations) the person detail page.

SEO: `seo_title` / `seo_description` with fallback to name / short description.

## Organisation organigram

Shows one organisation (selected in the FlexForm) with sections:

- `structure`
- `locations`
- `contacts`

## Route enhancers

Import `EXT:directory/Configuration/Sets/Directory/route-enhancers.yaml`. Example paths:

- German: `/profil/{slug}`, `/organisation/{slug}`
- English: `/profile/{slug}`, `/organization/{slug}`

Organigram sections: `/structure`, `/locations`, `/contacts`.
