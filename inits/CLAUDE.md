# inits/

Initialization sequence that sets up the PAD environment.

## Load Order (via `inits.php`)
1. `const.php` - Define constants (padLevelVars, padStrSto, etc.)
2. `lib.php` - Auto-load all files from `lib/` directory
3. `vars.php` - Initialize global variables
4. `clean.php` - Clean input data
5. `page.php` - Determine page to process
6. `ids.php` - Generate session/request IDs
7. `config.php` - Load configuration
8. `nono.php` - Handle "no PAD" mode
9. `fast.php` - Fast-path optimizations
10. `error.php` - Set up error handlers
11. `cookies.php` - Cookie handling
12. `client.php` - Client detection
13. `host.php` - Host configuration
14. `info.php` - Info/trace setup
15. `cache.php` - Cache initialization
16. `level.php` - Level system setup
17. `parms.php` - Parameter initialization
18. `app.php` - Application-specific initialization

## Key Constants Defined
- `padLevelVars` - Array of level-scoped variable names
- `padStrSto` - Store variable names
- `padOptionsStart` / `padOptionsEnd` - Option processing order
- `PQ`, `PT` - Sequence paths
