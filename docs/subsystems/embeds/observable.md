# Observable classic notebooks

`{embed:observable 'https://observablehq.com/@d3/sortable-bar-chart', cell='chart,viewof order', width=640, height=400, title='Sortable bar chart'/}` uses the classic public-notebook iframe contract. Accepts named notebooks, `/d/<16-hex-id>`, optional pinned version `@123`, and embed URLs; repeated `cell` query parameters are preserved unless `cell=` overrides them. Up to 20 named cells (including viewof/mutable names) are accepted; otherwise the notebook iframe chooses its default content. Width/height set the responsive aspect ratio and must each be 100–2000. Private notebook keys are rejected. This does not execute the Observable runtime in the host page and does not claim support for the new Notebooks 2.0 document API.

The shared consent facade loads no remote resource before activation. `padEmbedSources()` includes observablehq.com and old.observablehq.com because classic notebooks may redirect during the platform migration. Fallback links and iframe titles remain accessible.

[Observable classic embed documentation](https://observablehq.github.io/documentation/embeds/). Test: `php apps/regression/subsystems/embed-observable.php`.
