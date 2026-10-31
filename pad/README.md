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
├── charts/       # Chart subsystem (a kind per file of charts/types/)
├── codes/         # Codes subsystem (a barcode symbology per file of codes/types/)
├── constructs/    # @name@ construct handlers
├── data/          # Data format handlers (CSV, JSON, XML, YAML)
├── database/      # SQL schema definitions
├── embeds/        # Embed subsystem (a provider per file of embeds/types/)
├── error/         # Error handling
├── eval/          # Expression parser
├── events/        # Event hooks
├── exits/         # Shutdown and output handling
├── fakes/         # Fake data subsystem (a kind per file of fakes/types/)
├── functions/     # Pipe functions
├── games/         # Games subsystem (a game per file of games/types/)
├── get/           # Content retrieval
├── handling/      # Data post-processing (sort, page, dedup)
├── icons/         # Icon subsystem (an icon per file of icons/types/)
├── info/          # Debugging and tracing
├── inits/         # Initialization
├── inputs/        # Rich form fields (a kind per file of inputs/types/)
├── install/       # Installation scripts
├── level/         # Tag processing
├── lib/           # PHP helpers
├── maps/          # Map subsystem (a view per file of maps/types/)
├── occurrence/    # Data iteration
├── options/       # Tag options
├── properties/    # Tag properties
├── rules/         # Validation rules (a rule per file of rules/types/)
├── select/        # Select subsystem (declared tables)
├── sequence/      # Sequence subsystem
├── start/         # Execution lifecycle
├── tags/          # Template tags
├── toys/          # Toys subsystem (a toy per file of toys/types/)
├── try/           # Try/catch wrapper
├── types/         # Tag type handlers
├── walk/          # Template tree walking
└── widgets/       # Widgets subsystem (a widget per file of widgets/types/)
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
| charts/ | The chart subsystem - `{chart}`, `{sparkline}` and the `chart:` type; a chart kind is a file of `charts/types/`, its drawing in `charts/_lib/` |
| codes/ | The codes subsystem - `{barcode}`, `{qr}` and the `barcode:` type; a symbology is a file of `codes/types/`, its encoder and the SVG writers in `codes/_lib/` |
| constructs/ | `@name@` construct handlers (page, content, start, end, tidy) |
| data/ | Data format handlers (CSV, JSON, XML, YAML) |
| database/ | SQL schema definitions for PAD internal database |
| embeds/ | The embed subsystem - `{embed}`, the `embed:` type and the addresses of `{video}`; a provider is a file of `embeds/types/` - the addresses it knows and the frame it writes - the click-to-load facade and `{video}`'s own markup in `embeds/_lib/` |
| error/ | Boot-time and runtime error handling |
| eval/ | Expression parser and evaluator |
| events/ | Event hooks of the info modes (trace, stats, xref) - an application's own hooks are its `_events/`, run by lib/events.php |
| exits/ | Shutdown, output formatting, and HTTP responses |
| fakes/ | The fake data subsystem - `{fake}` and the `fake:` type; a kind is a file of `fakes/types/`, answering its value from the seeded sequence of `lib/fake.php` (`padFake`, `padFakeRows`) |
| functions/ | Pipe functions (trim, upper, date, html, etc.) |
| games/ | The games subsystem - `{game}` and the `game:` type; a game is a file of `games/types/`, its `.js` and `.css` in `games/assets/` beside the runtime's, the sudoku and crossword code in `games/_lib/` |
| get/ | Variable getter system for content retrieval |
| handling/ | Data array post-processing (sort, page, dedup) |
| icons/ | The icon subsystem - `{icon}` and the `icon:` type; an icon is a file of `icons/types/`, `<name>.svg`, drawn by `icons/_lib/icon.php` |
| info/ | Debugging, tracing, and profiling system |
| inits/ | Framework initialization and bootstrapping |
| inputs/ | The rich form fields - the `input:` type and the bare kinds, `{slider}`, `{stars}`, `{datepicker}` ...; a kind is a file of `inputs/types/` writing a native field, its `.js` and `.css` in `inputs/assets/` beside the runtime's, the shared field code in `inputs/_lib/input.php` |
| install/ | Installation scripts for database and web server |
| level/ | Tag processing and scope management |
| lib/ | Core utility functions (database, file, HTTP, etc.), loaded on every request - the code of one tag is `tags/_lib/<tag>.php`, of one engine feature the `_lib/` of its directory, included when it runs |
| maps/ | The map subsystem behind `{map}`; a view is a file of `maps/types/` - its box of longitude and latitude, for a region its countries - the outlines and the drawing in `maps/_lib/` |
| occurrence/ | Template iteration tracking and state management |
| options/ | Tag options processing (quote, glue, toData, etc.) |
| properties/ | Tag property accessors (first@, last@, count@, etc.) |
| rules/ | The validation rules of `padValidate`, `rules=` and the browser's checker - a rule is a file of `rules/types/` (its check, message and argument), its check in the browser `rules/assets/<rule>.js` beside the checker's runtime `rules/assets/validate.js` |
| select/ | The Select subsystem - a `$padSelect` table as a tag, its options (`where`, `order`, `join` ...) in `select/types/` |
| sequence/ | Mathematical sequence generation (80+ types) |
| start/ | Execution engine and context management |
| tags/ | Template tags (if, while, data, set, echo, etc.) |
| toys/ | The toys subsystem - `{toy}` and the `toy:` type; a toy is a file of `toys/types/`, its `.js` and `.css` in `toys/assets/` beside the runtime's `toy.js` and `toy.css`, the frame and the option checks in `toys/_lib/` |
| try/ | Try/catch exception handling wrapper |
| types/ | Tag type handlers (data, field, function, etc.) |
| walk/ | Tree walking and iteration utilities |
| widgets/ | The widgets subsystem - `{widget}` and the `widget:` type, reached bare as `{tabs}`, `{modal}` ...; a widget is a file of `widgets/types/` (its tag code), its functions in `widgets/_lib/`, its `.css` and `.js` in `widgets/assets/` |
