# Images

`image:kind` selects a presentation of local pictures. The original `pad:img` tag is replaced by `image:thumbnail`. All kinds share the GD decoder, EXIF orientation, source path checks, atomic thumbnail cache and `filter=` pipeline.

| Kind | Purpose |
| --- | --- |
| thumbnail | The original responsive 1x/2x thumbnail, cover/contain crop, lazy loading and placeholders. |
| picture | A `<picture>` with media sources and actual generated width variants, for responsive layouts. |
| figure | A thumbnail inside `<figure>`, with escaped `caption=` text. |
| contactsheet | A single cached PNG composed from image rows, followed by an accessible ordered caption list. |

```pad
{image:thumbnail 'photos/harbour.jpg', width=240, height=180, focusx=70, alt='Harbour'/}
{image:picture 'photos/harbour.jpg', width=640, widths='320,640,960,1280', sizes='(max-width: 640px) 100vw, 640px', alt='Harbour'/}
{image:figure 'photos/harbour.jpg', width=320, filter='grayscale', caption='The harbour in winter.', alt='Boats at their moorings'/}
{image:contactsheet data='photos', width=200, height=150, columns=3, src='file', caption='title', alt='Trip photographs'/}
```

`alt=` is required for thumbnail, picture and figure; an empty string marks decoration. Width, height, fit, format, quality and filter options keep their original behaviour. `focusx=` and `focusy=` are source-image percentages (0–100, default 50) around which a cover crop is centred, clamped at the edges. They have no effect on contain. Crops are part of the existing cache key.

For picture, `widths=` is a comma list or array of up to eight distinct positive pixel widths, each at most 4096. Duplicate widths and widths beyond the source resolution collapse; pictures are never upscaled. `sizes=` describes the displayed width to the browser. Source media breakpoints use the generated widths, and each source offers the available variants so the browser can select for device pixel density. Filters use the same options at each density and retain their pixel budgets. Pixel filters remain sharp. ASCII and halftone replace image markup and are therefore supported by thumbnail and figure, but rejected for picture and contactsheet.

Contact sheets accept `data=` or JSON/CSV/YAML between the pair. Every row has `src` and an optional `caption` (falling back to `alt` then the filename); `src=` and `caption=` select different fields. Limits are 64 rows, 1–8 columns, cells of at most 1024 × 1024 and a 16-megapixel final sheet. Missing or unsafe sources retain the shared image errors. Captions are escaped text. Contact sheets are static image composites, distinct from the existing interactive gallery widgets. Image and placeholder filters are shared; placeholders do not become part of the finished composite.

Sources stay inside `www/<application>/` after symlink resolution. No image kind downloads remote content on the server. Cached files remain in `www/<application>/_thumbs/`, and the existing public PHP helpers now live in `subs/images/_lib/img.php`.
