# Generated cards, noise removal and geometry

```pad
{image:socialcard title='A practical guide', description='Deterministic local images for sharing.', brand='PAD', font='noto-sans', width=1200, height=630/}
{image:thumbnail 'photos/harbour.jpg', width=400, filter='median', alt='Harbour'/}
{pattern:penrose width=640, height=360, size=55, seed=5/}
{pattern:interlace width=640, height=360, size=40/}
{avatar:geometric 'Ada Lovelace', style='rings', size=64/}
```

Social cards are cached PNGs with alt text and a download link. `logo` accepts only a confined local image (up to four megapixels). Dimensions are 600–1600 by 315–900, with a 1.44-megapixel ceiling; title/description/brand limits are 180/400/60 Unicode characters. Text wraps and title text shrinks to fit; overflow reports an error. The packaged Noto Sans font is the only supported `font`; missing glyphs and complex shaping scripts fail explicitly. Font contents, text, dimensions and logo contents participate in the cache key. GD with FreeType is required. Same source and runtime produce reproducible output; different FreeType versions can rasterize differently.

`median` uses a fixed 3×3 neighborhood with clamped border coordinates. RGB channels take their separate median; the centre's alpha is preserved. Fully transparent neighbors use the centre's RGB so hidden colors do not bleed. The shared two-megapixel PHP pixel-loop limit applies.

`penrose` uses bounded Robinson-triangle deflation from a tenfold sun, with golden-ratio subdivision. The viewport crops the finite patch. **It does not repeat seamlessly.** Size controls subdivision density; at most 50,000 triangles. Seed sets initial rotation. See [Bettencourt's mathematical construction](https://www.cs.toronto.edu/~jessebett/projects/penrose-tiling/Thesis/Bettencourt_Penrose_Tiling_Thesis_2015_reducedsize.pdf).

`interlace` draws continuous diagonal strands with alternating crossings and a two-size repeat period. The existing geometric avatar styles remain the composition route for identity art; no redundant avatar alias was added.
