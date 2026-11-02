# saturation

`amount` 0..4 (default 1) interpolates encoded sRGB channels around Rec.709 luma (0.2126R + 0.7152G + 0.0722B). Zero gives gray, one is exact identity. This is not a perceptual color-space conversion.

All alpha bytes remain unchanged. The shared pixel pipeline caps work at 2 megapixels and includes options in derived-image cache identities. Preview and export use the same GD transform.

Use `{image:thumbnail "photos/harbour.jpg", width=400, filter="saturation", alt="Harbour"/}`.
