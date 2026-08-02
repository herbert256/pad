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
| constructs/ | `@name@` construct handlers (page, content, start, end, tidy) |
| data/ | Data format handlers (CSV, JSON, XML, YAML) |
| database/ | SQL schema definitions for PAD internal database |
| error/ | Boot-time and runtime error handling |
| eval/ | Expression parser and evaluator |
| events/ | Event hooks and lifecycle management |
| exits/ | Shutdown, output formatting, and HTTP responses |
| functions/ | Pipe functions (trim, upper, date, html, etc.) |
| get/ | Variable getter system for content retrieval |
| handling/ | Data array post-processing (sort, page, dedup) |
| info/ | Debugging, tracing, and profiling system |
| inits/ | Framework initialization and bootstrapping |
| install/ | Installation scripts for database and web server |
| level/ | Tag processing and scope management |
| lib/ | Core utility functions (database, file, HTTP, etc.) |
| occurrence/ | Template iteration tracking and state management |
| options/ | Tag options processing (quote, glue, toData, etc.) |
| properties/ | Tag property accessors (first@, last@, count@, etc.) |
| sequence/ | Mathematical sequence generation (80+ types) |
| start/ | Execution engine and context management |
| tags/ | Template tags (if, while, data, set, echo, etc.) |
| try/ | Try/catch exception handling wrapper |
| types/ | Tag type handlers (data, field, function, etc.) |
| walk/ | Tree walking and iteration utilities |
