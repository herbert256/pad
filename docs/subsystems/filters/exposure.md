# exposure

`stops` -8..8 (default 0) multiplies linear-light sRGB intensity by 2^stops, then encodes sRGB and clips. Zero is an exact identity.

All alpha bytes remain unchanged. The shared pixel pipeline caps work at 2 megapixels and includes options in derived-image cache identities. Preview and export use the same GD transform.

Use `{image:thumbnail "photos/harbour.jpg", width=400, filter="exposure", alt="Harbour"/}`.
