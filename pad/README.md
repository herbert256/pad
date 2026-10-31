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
├── constructs/    # @name@ construct handlers
├── data/          # Data format handlers (CSV, JSON, XML, YAML)
├── database/      # SQL schema definitions
├── error/         # Error handling
├── eval/          # Expression parser
├── events/        # Event hooks
├── exits/         # Shutdown and output handling
├── functions/     # Pipe functions
├── games/         # Games subsystem (a game per file of games/types/)
├── get/           # Content retrieval
├── handling/      # Data post-processing (sort, page, dedup)
├── info/          # Debugging and tracing
├── inits/         # Initialization
├── install/       # Installation scripts
├── level/         # Tag processing
├── lib/           # PHP helpers
├── maps/          # Map subsystem (a view per file of maps/types/)
├── occurrence/    # Data iteration
├── options/       # Tag options
├── properties/    # Tag properties
├── select/        # Select subsystem (declared tables)
├── sequence/      # Sequence subsystem
├── start/         # Execution lifecycle
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
| charts/ | The chart subsystem - `{chart}`, `{sparkline}` and the `chart:` type; a chart kind is a file of `charts/types/`, its drawing in `charts/_lib/` |
| constructs/ | `@name@` construct handlers (page, content, start, end, tidy) |
| data/ | Data format handlers (CSV, JSON, XML, YAML) |
| database/ | SQL schema definitions for PAD internal database |
| error/ | Boot-time and runtime error handling |
| eval/ | Expression parser and evaluator |
| events/ | Event hooks of the info modes (trace, stats, xref) - an application's own hooks are its `_events/`, run by lib/events.php |
| exits/ | Shutdown, output formatting, and HTTP responses |
| functions/ | Pipe functions (trim, upper, date, html, etc.) |
| games/ | The games subsystem - `{game}` and the `game:` type; a game is a file of `games/types/`, its `.js` and `.css` in `games/assets/` beside the runtime's, the sudoku and crossword code in `games/_lib/` |
| get/ | Variable getter system for content retrieval |
| handling/ | Data array post-processing (sort, page, dedup) |
| info/ | Debugging, tracing, and profiling system |
| inits/ | Framework initialization and bootstrapping |
| install/ | Installation scripts for database and web server |
| level/ | Tag processing and scope management |
| lib/ | Core utility functions (database, file, HTTP, etc.), loaded on every request - the code of one tag is `tags/_lib/<tag>.php`, of one engine feature the `_lib/` of its directory, included when it runs |
| maps/ | The map subsystem behind `{map}`; a view is a file of `maps/types/` - its box of longitude and latitude, for a region its countries - the outlines and the drawing in `maps/_lib/` |
| occurrence/ | Template iteration tracking and state management |
| options/ | Tag options processing (quote, glue, toData, etc.) |
| properties/ | Tag property accessors (first@, last@, count@, etc.) |
| select/ | The Select subsystem - a `$padSelect` table as a tag, its options (`where`, `order`, `join` ...) in `select/types/` |
| sequence/ | Mathematical sequence generation (80+ types) |
| start/ | Execution engine and context management |
| tags/ | Template tags (if, while, data, set, echo, etc.) |
| try/ | Try/catch exception handling wrapper |
| types/ | Tag type handlers (data, field, function, etc.) |
| walk/ | Tree walking and iteration utilities |
