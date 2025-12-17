# PAD Applications

This document describes the structure and types of PAD applications.

## Application Types

| Type | Description |
|------|-------------|
| Standard | Full PAD application with templates (.pad files) |
| CLI | Command-line interface application |
| Minimal | Simple single-page application |
| Plain PHP | PHP application without PAD templating |

## Application Location

Applications are stored in the `apps/` directory:

```
apps/
├── pad/           # Reference application and PAD manual
├── myapp/         # Your custom application
└── demo/          # Demo application
```

## Creating Applications

See [APP.md](APP.md) for complete instructions on creating and developing PAD applications.

## Framework Documentation

For information about the PAD framework internals, see [pad/README.md](pad/README.md).
