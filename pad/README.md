# PAD Framework

PAD (PHP Application Driver) is an Inversion of Control PHP template engine. Instead of PHP code including templates, PAD templates drive the execution flow, orchestrating data access and output generation.

## Directory Structure

```
pad/
├── build/         # Page assembly
├── cache/         # Caching system
├── config/        # Configuration
├── error/         # Error handling
├── eval/          # Expression parser
├── functions/     # Pipe functions
├── inits/         # Initialization
├── level/         # Tag processing
├── lib/           # PHP helpers
├── occurrence/    # Data iteration
├── options/       # Tag options
├── properties/    # Tag properties
├── sequence/      # Sequence subsystem
├── start/         # Execution lifecycle
├── tags/          # Template tags
├── types/         # Tag type handlers
└── walk/          # Template tree walking
```

## Documentation

For complete framework documentation, see [docs/pad/README.md](../docs/pad/README.md).

## Module Documentation

| Directory | README | Description |
|-----------|--------|-------------|
| at/ | [README.md](at/README.md) | @ symbol property accessor for dynamic property resolution |
| build/ | [README.md](build/README.md) | Page assembly and template compilation |
| cache/ | [README.md](cache/README.md) | Server-side caching with file, memory, and database backends |
| call/ | [README.md](call/README.md) | PHP file inclusion with output buffering and error handling |
| callback/ | [README.md](callback/README.md) | Callback execution at lifecycle stages |
| config/ | [README.md](config/README.md) | Framework configuration and presets |
| constructs/ | [README.md](constructs/README.md) | Core XML-like construct handlers (pad, page, content) |
| data/ | [README.md](data/README.md) | Data format handlers (CSV, JSON, XML, YAML) |
| database/ | [README.md](database/README.md) | SQL schema definitions for PAD internal database |
| error/ | [README.md](error/README.md) | Boot-time and runtime error handling |
| eval/ | [README.md](eval/README.md) | Expression parser and evaluator |
| events/ | [README.md](events/README.md) | Event hooks and lifecycle management |
| exits/ | [README.md](exits/README.md) | Shutdown, output formatting, and HTTP responses |
| functions/ | [README.md](functions/README.md) | Pipe functions (trim, upper, date, html, etc.) |
| get/ | [README.md](get/README.md) | Variable getter system for content retrieval |
| handling/ | [README.md](handling/README.md) | Data array post-processing (sort, page, dedup) |
| info/ | [README.md](info/README.md) | Debugging, tracing, and profiling system |
| inits/ | [README.md](inits/README.md) | Framework initialization and bootstrapping |
| install/ | [README.md](install/README.md) | Installation scripts for database and web server |
| level/ | [README.md](level/README.md) | Tag processing and scope management |
| lib/ | [README.md](lib/README.md) | Core utility functions (database, file, HTTP, etc.) |
| occurrence/ | [README.md](occurrence/README.md) | Template iteration tracking and state management |
| options/ | [README.md](options/README.md) | Tag options processing (quote, glue, toData, etc.) |
| properties/ | [README.md](properties/README.md) | Tag property accessors (first@, last@, count@, etc.) |
| sequence/ | [README.md](sequence/README.md) | Mathematical sequence generation (80+ types) |
| start/ | [README.md](start/README.md) | Execution engine and context management |
| tags/ | [README.md](tags/README.md) | Template tags (if, while, data, set, echo, etc.) |
| try/ | [README.md](try/README.md) | Try/catch exception handling wrapper |
| types/ | [README.md](types/README.md) | Tag type handlers (data, field, function, etc.) |
| walk/ | [README.md](walk/README.md) | Tree walking and iteration utilities |
