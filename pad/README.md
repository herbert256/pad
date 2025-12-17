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
