# unsharp

`radius` 1..50 CSS pixels (default 2), `amount` 0..4 (default 1), and `threshold` 0..255 channel units (default 0). Adds amount times the original-minus-blur difference, clipping channels; threshold suppresses small differences. Reuses the alpha-aware blur and its density scaling.

All alpha bytes remain unchanged. The shared pixel pipeline caps work at 2 megapixels and includes options in derived-image cache identities. Preview and export use the same GD transform.

Use `{image:thumbnail "photos/harbour.jpg", width=400, filter="unsharp", alt="Harbour"/}`.
