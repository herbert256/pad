# contourdots

Stippled topographic contours reuse the exact topography noise and marching-squares engine. `levels` 2..60, `spacing` 2..30 pixels. This is a finite field, not a seamless tile. The conservative 100,000-dot budget includes cell diagonal length; zero-length round dashes render dots without expanding point markup.

`{pattern:contourdots seed=17, width=240, height=160/}` uses the shared deterministic seed, escaped colours, theme integration and optional accessible `title`.
