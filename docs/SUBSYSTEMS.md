# Subsystem ownership and rendering tags

`pad/subs/` has 30 owners. A subsystem may contain several related kind registries.
Each registry still has a `types/` directory; the files in it remain the source of
truth. `pad/lib/subsystem.php` maps public namespaces and helper families to these
directories. A namespace is an interface, not a requirement for a top-level folder.

## Review of all 42 former directories

| Owner | Former directories now owned together | Reason and retained boundaries |
| --- | --- | --- |
| `ai` | ai | Model providers and response validation have their own contract. |
| `calendars` | calendars, times, holidays, recurrences | Date views, clocks, holiday data and repeated dates belong together. Holiday definitions and recurrence algorithms remain separate from rendering kinds. |
| `channels` | channels | Notification delivery providers and the notification store. |
| `charts` | charts, timelines | Numeric and dated data visualizations share row parsing and SVG support. Timeline views retain their date and span handling. |
| `codes` | codes | Machine-readable encodings need symbology-specific validation. |
| `constraints` | new | Bounded exact cover, matching and assignment return solutions and explicit completion status. |
| `diagrams` | diagrams | Structured diagram layout differs from numeric chart rendering. |
| `diffs` | diffs | Text and structured comparisons share change semantics, not merely markup. |
| `embeds` | embeds | External media providers, local media and consent-based loading. |
| `fakes` | fakes, distributions | Reproducible sample data and seeded statistical sampling; distribution parameter contracts remain separate. |
| `formats` | formats | Data readers and writers, including feeds and binary spreadsheets. |
| `games` | games, toys | Interactive boards, puzzles and simulations share browser asset and lifecycle infrastructure. |
| `graphs` | new | Graph algorithms return dependency order, components, routes and critical paths; rendering remains in charts and diagrams. |
| `icons` | icons | A named SVG asset registry, distinct from image generation and processing. |
| `images` | images, avatars, filters, patterns | Image views, identity images, transformations and generated artwork. Filter recipes and pattern recipes keep their existing APIs. |
| `inputs` | inputs, rules | Form controls and their server/browser validation rules. |
| `jsonld` | jsonld | Structured page metadata, not a visual component or generic format codec. |
| `maps` | maps, geospatial | Geographic views and geographic data operations. |
| `notations` | notations | Domain-specific notation parsers and renderers, including music and board positions. |
| `oauth` | oauth | Login provider protocols and token exchange remain separate from notification and AI providers. |
| `printables` | printables | Paper dimensions, pagination and PDF layout differ from screen widgets. |
| `search` | search | Indexing, query processing, stemming, ranking and excerpts. |
| `schemas` | new | Shared JSON, record and rowset validation; the AI boundary delegates to this core. |
| `sounds` | sounds | Synthesized audio recipes and WAV output. |
| `statistics` | new | Descriptive statistics and regression return reusable plain data. |
| `tables` | tables, sheets, formulas | Tabular presentation and computed grids; spreadsheet functions remain a function registry, not table views. |
| `themes` | themes, palettes | Page colours and reusable colour scales. |
| `textanalysis` | new | Unicode text counts, n-grams and lexical similarity; search ranking remains separate. |
| `units` | units | Dimension-checked conversion tables and operations have no equivalent in the other owners. |
| `widgets` | widgets, sections, effects, highlighters | Page components, larger page sections, visual effects and source-code presentation. Specialized rendering contracts remain separate. |

The moved families use paths such as `pad/subs/images/avatars/`,
`pad/subs/inputs/rules/` and `pad/subs/tables/formulas/`. Existing public family
namespaces (`avatar:`, `time:`, `timeline:`, `sheet:`, `section:`, `effect:`,
`pattern:` and `toy:`), PHP helper signatures and browser asset URLs are retained.
Direct PHP includes must use the new paths. No duplicate forwarding directories
are left at the old locations.

## Rendering tags

These tags now use existing subsystem namespaces. Update both ends of a pair.
Their options and rendering behavior are retained.
This changes rendering tags only: built-in pipes such as `markdown`, `highlight`
and the explicitly selected `pad:markdown` pipe keep their existing names.

| Former tag | Subsystem type |
| --- | --- |
| `pad:toc` | `widget:toc` |
| `pad:pager` | `widget:pager` |
| `pad:tab` | `widget:tab` |
| `pad:source` | `widget:source` |
| `pad:highlight` | `widget:highlight` |
| `pad:markdown` | `widget:markdown` |
| `pad:excerpt` | `widget:excerpt` |
| `pad:lorem` | `widget:lorem` |
| `pad:shadow` | `widget:shadow` |
| `pad:reactData` | `widget:reactData` |
| `pad:notifications` | `widget:notifications` |
| `pad:oauth` | `widget:oauth` |
| `pad:theme` | `widget:theme` |
| `pad:themes` | `widget:themes` |
| `pad:palette` | `widget:palette` |
| `pad:video` | `widget:video` |
| `pad:emoji` | `image:emoji` |
| `pad:country` | `image:country` |
| `pad:form` | `input:form` |
| `pad:input` | `input:field` |
| `pad:textarea` | `input:textarea` |
| `pad:validator` | `input:validator` |

The widget types for video, themes, palettes, OAuth and notifications are UI
adapters over their owning subsystem's existing helpers. They do not duplicate
provider definitions or implement a second protocol. An OAuth login button is a
widget; an OAuth provider remains a provider. A video widget preserves the
existing local-video and poster behavior; `embed:*` remains the provider API.

```html
{widget:toc levels='2-3'/}
{widget:tabs}
  {widget:tab 'Overview'}<p>Overview</p>{/widget:tab}
  {widget:tab 'Details'}<p>Details</p>{/widget:tab}
{/widget:tabs}
{input:form 'contact', client}
  {input:field 'email', type='email', label='Email', rules='required|email'/}
  {input:textarea 'message', label='Message', rows=6/}
  <button>Send</button>
{/input:form}
```

## Core tag boundary

`pad/tags/` owns evaluation, control flow, variables and stores, iteration,
template composition, request/response handling, authorization, file and network
access, and diagnostics. Producing a string does not itself make a tag a visual
component.

In particular, `code` executes a PAD pass; `tree`, `branch` and `recurse` implement
recursive iteration; `attrs` and `classes` serialize template attributes; `asset`,
`vite`, `nonce` and `csrf` integrate assets and security with the response;
`sitemap` discovers and exports routes; `flash` consumes session messages;
`meta`, `fragment`, `live`, `ajax`, `block`, `slot`, `push` and `stack` participate
in the page lifecycle. These stay in the engine. `ignore`, `tidy` and `spaceless`
control parsing or transform existing output rather than build a new component.

Add a rendering feature as a type of an existing owner first. Create a new
top-level subsystem only when its responsibility and execution contract do not
fit an existing owner. Keep provider/data registries separate from view registries
when their files return different kinds of values.

## Showcase applications

Showcases follow the subsystem owners instead of retaining separate apps for each family:

| Application | Combined galleries |
| --- | --- |
| `games` | Games, toys and simulations, plus Crossword and Sudoku examples |
| `images` | The former Patterns app, image filters, pictures, avatars, emoji and country labels |
| `tables` | The former Sheets app, formula reference and data-table examples |
| `widgets` | The former Sections app, page components, text widgets, video, sections and effects; themes and palettes remain available here as page-design companions |
| `calendars` | Calendar views, relative times and countdowns |
| `charts` | Numeric charts and timelines |

The general `showcase` keeps families without a dedicated owner app and links to
these combined galleries. The 63 relocated examples live once under
`apps/<owner>/examples/<family>/`; a shared catalog and example layout read their
own metadata, templates and paired PHP. Every owner app exposes them in its menu
and home page.

Old `/toys/`, `/patterns/`, `/sheets/` and `/sections/` entry points return 308
redirects to the canonical owner. Moved `/showcase/?<family>/<example>` links also
redirect. Both query and clean routes retain their parameters; templates and assets
are maintained in the owner app only. The public `toy:`, `pattern:`, `sheet:` and
`section:` namespaces stay unchanged.

The app integration checks run with `php tests/subsystems/showcase-apps.php`.
For desktop and small-screen browser checks, run
`node tests/subsystems/showcase-browser.cjs` with local Apache and Chrome available.
`pages.sh` exports the canonical galleries, shared subsystem assets and redirect
pages for the retired showcase bookmarks.
