# info/

Debugging, tracing, and statistics collection system.

## Structure
- `start/` - Info collection initialization
- `end/` - Info collection finalization
- `_lib/` - Shared info utilities
- `types/` - Info type handlers

## Info Types (`types/`)
- `stats/` - Performance statistics
- `trace/` - Execution tracing (level/, occur/)
- `track/` - Variable/state tracking
- `xml/` - XML output format (level/, occur/)
- `xref/` - Cross-reference generation

## Activation
Set `$padInfo` in config to enable info collection. Multiple info types can be combined.

## Usage
Provides detailed visibility into template processing for debugging and optimization.
