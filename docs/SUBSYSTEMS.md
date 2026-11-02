# Subsystem layout and rendering tags

All 53 subsystems live exactly one directory below `pad/subs/`. Each subsystem
has its own `types/` registry; the files in it remain the source of truth.
`pad/lib/subsystem.php` maps public namespaces and helper families to these
single-directory names. Subsystems must not be nested inside other subsystems.
Internal implementation directories such as `_lib/`, `types/` and `fonts/` remain
inside their subsystem.

## Related subsystems

Related families are siblings on disk. Applications may still combine their
examples and galleries independently of the engine directory layout.

| Area | Sibling directories directly under `pad/subs/` |
| --- | --- |
| Dates and time | `calendars`, `times`, `holidays`, `recurrences`, `intervals`, `timeseries` |
| Charts | `charts`, `timelines` |
| Sample data and numeric analysis | `fakes`, `distributions`, `numerics`, `signals` |
| Games and simulations | `games`, `toys` |
| Images | `images`, `avatars`, `filters`, `patterns` |
| Forms | `inputs`, `rules` |
| Geography and planar geometry | `maps`, `geospatial`, `geometry` |
| Tables and spreadsheets | `tables`, `sheets`, `formulas` |
| Colour | `themes`, `palettes`, `colors` |
| Page components | `widgets`, `sections`, `effects`, `highlighters` |

The other sibling subsystems are `ai`, `channels`, `codes`, `constraints`,
`diagrams`, `diffs`, `embeds`, `formats`, `graphs`, `icons`, `jsonld`, `notations`,
`oauth`, `printables`, `schemas`, `search`, `sounds`, `statistics`, `textanalysis`
and `units`.

Use paths such as `pad/subs/avatars/`, `pad/subs/rules/` and
`pad/subs/formulas/`. Public family namespaces (`avatar:`, `time:`, `timeline:`,
`sheet:`, `section:`, `effect:`, `pattern:` and `toy:`), PHP helper signatures and
browser asset URLs are retained. Direct PHP includes and `padKinds()` calls use
the flat paths and registry names. No forwarding directories or nested aliases
are left at the former locations.

`php tests/subsystems/layout.php` checks the registry and disk layout; it also
runs in the subsystem test suite and `./ci.sh`.

## Computational helpers

These six families return bounded plain data. They do not introduce template tag
namespaces. Their four kinds each are discovered through `padKinds()` and the
helper catalog, just like statistics, units and graph algorithms.

| Directory | Public helper | Kinds | Contract |
| --- | --- | --- | --- |
| `intervals` | `padIntervals` | `union`, `intersection`, `subtract`, `overlaps` | Half-open numeric or instant intervals; typed groups; source indices on overlap pairs |
| `colors` | `padColor` | `convert`, `mix`, `contrast`, `gamut` | Explicit sRGB, linear sRGB and HSL; alpha compositing and clipping policy |
| `timeseries` | `padTimeSeries` | `resample`, `rolling`, `ewma`, `difference` | Fixed-duration buckets, count windows, duplicate and missing-value policies |
| `geometry` | `padGeometry` | `hull`, `area`, `centroid`, `clip` | Planar finite coordinates; simple rings; convex rectangle clipping |
| `numerics` | `padNumeric` | `interpolate`, `integrate`, `root`, `linear-system` | Bounded methods with convergence and residual metadata |
| `signals` | `padSignal` | `spectrum`, `convolve`, `correlate`, `window` | Offline finite samples with explicit scaling, sample rate and edge conventions |

Read each family's `pad/subs/<directory>/README.md` for inputs, limits and numerical
assumptions. Runnable examples live under `apps/tags/examples/` using public family
keys `interval`, `color`, `timeseries`, `geometry`, `numeric` and `signal`.
Calendar availability delegates interval merging/subtraction to the interval core.
Palette and avatar luminance share the color core, retaining their previous output.

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

Showcases keep their combined galleries even though the subsystem directories are flat:

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
