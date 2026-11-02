# PAD Framework

PAD (PHP Application Driver) is an Inversion of Control PHP template engine. Instead of PHP code including templates, PAD templates drive the execution flow, orchestrating data access and output generation.

## Directory Structure

```
pad/
├── at/            # @ symbol property accessor
├── build/         # Page assembly
├── cache/         # Caching system
├── call/          # PHP file inclusion wrappers
├── callback/      # Callback execution
├── config/        # Configuration
├── constructs/    # @name@ construct handlers
├── data/          # Data format handlers (CSV, JSON, XML, YAML)
├── database/      # SQL schema definitions
├── error/         # Error handling
├── eval/          # Expression parser
├── events/        # Event hooks
├── exits/         # Shutdown and output handling
├── functions/     # Pipe functions
├── get/           # Content retrieval
├── handling/      # Data post-processing (sort, page, dedup)
├── info/          # Debugging and tracing
├── inits/         # Initialization
├── install/       # Installation scripts
├── level/         # Tag processing
├── lib/           # PHP helpers
├── occurrence/    # Data iteration
├── options/       # Tag options
├── properties/    # Tag properties
├── select/        # Select subsystem (declared tables)
├── sequence/      # Sequence subsystem
├── start/         # Execution lifecycle
├── subs/          # The directory-driven subsystems - their kinds the files of each one's types/
│   ├── charts/    # Chart subsystem (a kind per file of types/)
│   ├── codes/     # Codes subsystem (a barcode symbology per file of types/)
│   ├── diagrams/  # Diagram subsystem (a kind per file of types/)
│   ├── embeds/    # Embed subsystem (a provider per file of types/)
│   ├── fakes/     # Fake data subsystem (a kind per file of types/)
│   ├── games/     # Games subsystem (a game per file of types/)
│   ├── icons/     # Icon subsystem (an icon per file of types/)
│   ├── inputs/    # Rich form fields (a kind per file of types/)
│   ├── jsonld/    # JSON-LD subsystem (a Schema.org kind per file of types/)
│   ├── maps/      # Map subsystem (a view per file of types/)
│   ├── printables/ # Printables subsystem (a kind of paper per file of types/)
│   ├── calendars/ # Gregorian calendar views (a view per file of types/)
│   ├── notations/ # Notations subsystem (a notation per file of types/)
│   ├── rules/     # Validation rules (a rule per file of types/)
│   ├── sounds/    # Sounds subsystem (a recipe per file of types/)
│   ├── toys/      # Toys subsystem (a toy per file of types/)
│   └── widgets/   # Widgets subsystem (a widget per file of types/)
├── tags/          # Template tags
├── try/           # Try/catch wrapper
├── types/         # Tag type handlers
└── walk/          # Template tree walking
```

## Documentation

For complete framework documentation, see [docs/PAD.md](../docs/PAD.md).

## Module Overview

| Directory | Description |
|-----------|-------------|
| at/ | @ symbol property accessor for dynamic property resolution |
| build/ | Page assembly and template compilation |
| cache/ | Server-side caching with file, memcached, redis, apcu, and database backends |
| call/ | PHP file inclusion with output buffering and error handling |
| callback/ | Callback execution at lifecycle stages |
| config/ | Framework configuration and presets |
| constructs/ | `@name@` construct handlers (page, content, start, end, tidy) |
| data/ | Data format handlers (CSV, JSON, XML, YAML) |
| database/ | SQL schema definitions for PAD internal database |
| error/ | Boot-time and runtime error handling |
| eval/ | Expression parser and evaluator |
| events/ | Event hooks of the info modes (trace, stats, xref) - an application's own hooks are its `_events/`, run by lib/events.php |
| exits/ | Shutdown, output formatting, and HTTP responses |
| functions/ | Pipe functions (trim, upper, date, html, etc.) |
| get/ | Variable getter system for content retrieval |
| handling/ | Data array post-processing (sort, page, dedup) |
| info/ | Debugging, tracing, and profiling system |
| inits/ | Framework initialization and bootstrapping |
| install/ | Installation scripts for database and web server |
| level/ | Tag processing and scope management |
| lib/ | Core utility functions (database, file, HTTP, etc.), loaded on every request - the code of one tag is `tags/_lib/<tag>.php`, of one engine feature the `_lib/` of its directory, included when it runs |
| occurrence/ | Template iteration tracking and state management |
| options/ | Tag options processing (quote, glue, toData, etc.) |
| properties/ | Tag property accessors (first@, last@, count@, etc.) |
| select/ | The Select subsystem - a `$padSelect` table as a tag, its options (`where`, `order`, `join` ...) in `select/types/` |
| sequence/ | Mathematical sequence generation (80+ types) |
| start/ | Execution engine and context management |
| subs/charts/ | The chart subsystem - `{chart:bar}`, `{chart:sparkline}` and the `chart:` type; a chart kind is a file of `subs/charts/types/`, its drawing in `subs/charts/_lib/` |
| subs/codes/ | The codes subsystem - `{barcode:auto}`, `{barcode:qr}` and the `barcode:` type; a symbology is a file of `subs/codes/types/`, its encoder and the SVG writers in `subs/codes/_lib/` |
| subs/diagrams/ | The diagram subsystem - `{diagram:auto}`, `{diagram:mindmap}`, `{diagram:gitgraph}`, `{diagram:fishbone}`, `{diagram:railroad}`, `{diagram:packet}` and the `diagram:` type; a kind is a file of `subs/diagrams/types/` laying out the lines between the tags as inline SVG |
| subs/embeds/ | The embed subsystem - `{embed:auto}`, the `embed:` type and the addresses of `{pad:video}`; a provider is a file of `subs/embeds/types/` - the addresses it knows and the frame it writes - the click-to-load facade and `{pad:video}`'s own markup in `subs/embeds/_lib/` |
| subs/fakes/ | The fake data subsystem - `{fake:rows}` and the `fake:` type; a kind is a file of `subs/fakes/types/`, answering its value from the seeded sequence of `lib/fake.php` (`padFake`, `padFakeRows`) |
| subs/games/ | The games subsystem - `{game:memory}` and the `game:` type; a game is a file of `subs/games/types/`, its `.js` and `.css` in `www/assets/games/` beside the runtime's, the sudoku and crossword code in `subs/games/_lib/` |
| subs/icons/ | The icon subsystem - `{icon:star}` and the `icon:` type; an icon is a file of `subs/icons/types/`, `<name>.svg`, drawn by `subs/icons/_lib/icon.php` |
| subs/inputs/ | The rich form fields - the `input:` type and its qualified kinds, `{input:slider}`, `{input:stars}`, `{input:datepicker}` ...; a kind is a file of `subs/inputs/types/` writing a native field, its `.js` and `.css` in `www/assets/inputs/` beside the runtime's, the shared field code in `subs/inputs/_lib/input.php` |
| subs/jsonld/ | The JSON-LD subsystem - `{jsonld:article}`, the `jsonld:` type, never bare, and `padJsonLd`; a kind is a file of `subs/jsonld/types/` answering its Schema.org type and how each field maps onto it, `subs/jsonld/_lib/jsonld.php` the mapping, the values (absolute addresses, ISO 8601 dates) and the script, `subs/jsonld/_lib/values.php` the values of the later kinds (durations, lists, opening hours, salaries), `jsonld.php` the body both forms run |
| subs/maps/ | The map subsystem behind `{pad:map}`; a view is a file of `subs/maps/types/` - its box of longitude and latitude, for a region its countries - the outlines and the drawing in `subs/maps/_lib/` |
| subs/jsonld/ | The JSON-LD subsystem - `{jsonld:article}`, the `jsonld:` type, never bare, and `padJsonLd`; a kind is a file of `subs/jsonld/types/` answering its Schema.org type and how each field maps onto it, `subs/jsonld/_lib/jsonld.php` the mapping, the values (absolute addresses, ISO 8601 dates) and the script, `jsonld.php` the body both forms run |
| subs/maps/ | The map subsystem behind `{pad:map}`; a view is a file of `subs/maps/types/` - its box of longitude and latitude, for a region its countries, for the states or provinces of a country the outlines file of its own - the outlines and the drawing in `subs/maps/_lib/` |
| subs/calendars/ | Gregorian month, mini, week, day, agenda, quarter and year views through `{calendar:kind}`. Shared event, holiday and locale handling; pair rows for custom markup. |
| subs/notations/ | The notations subsystem - `{notation:math}` and the `notation:` type, written as `{notation:math}`, `{notation:braille}` ... - `{notation:chess}` draws the chess notation; a notation is a file of `subs/notations/types/`, its drawing in `subs/notations/_lib/<kind>.php`, its style in `www/assets/notations/<kind>.css`, `notation.php` the body every form runs |
| subs/rules/ | The validation rules of `padValidate`, `rules=` and the browser's checker - a rule is a file of `subs/rules/types/` (its check, message and argument), its check in the browser `www/assets/rules/<rule>.js` beside the checker's runtime `www/assets/rules/validate.js` |
| subs/printables/ | The printables subsystem - `{printable:labels}` and the `printable:` type, never bare; a kind is a file of `subs/printables/types/` answering its plan - the paper, the places of the items, the boxes, rectangles and lines they draw - laid out by `subs/printables/_lib/printable.php` as mm-based CSS and the `data-pdf-*` attributes the PDF writer (`tags/_lib/pdf.php`) reads; `assets/printable.css` prints the sheets alone |
| subs/sounds/ | The sounds subsystem - `{sound:chime}` and the `sound:` type, never bare; a sound is a recipe, a file of `subs/sounds/types/`, rendered by `subs/sounds/_lib/sound.php` (the WAV, `padSoundWav`) and by `www/assets/sounds/sound.js` in the browser with the same arithmetic |
| subs/toys/ | The toys subsystem - `{toy:life}` and the `toy:` type; a toy is a file of `subs/toys/types/`, its `.js` and `.css` in `www/assets/toys/` beside the runtime's `toy.js` and `toy.css`, the frame and the option checks in `subs/toys/_lib/` |
| subs/widgets/ | The widgets subsystem - `{widget:tabs}` and the `widget:` type, written as `{widget:tabs}`, `{widget:modal}` ...; a widget is a file of `subs/widgets/types/` (its tag code), its functions in `subs/widgets/_lib/`, its `.css` and `.js` in `www/assets/widgets/` |
| tags/ | Template tags (if, while, data, set, echo, etc.) |
| try/ | Try/catch exception handling wrapper |
| types/ | Tag type handlers (data, field, function, etc.) |
| walk/ | Tree walking and iteration utilities |
