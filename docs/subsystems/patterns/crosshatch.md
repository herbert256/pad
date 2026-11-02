# crosshatch

Print-friendly diagonal strokes. `size` is spacing, `angle` rotates the first stroke family, `weight` is stroke width (clipped to spacing), and `cross=0` removes the perpendicular second family. Each family uses a separately rotated tile, so arbitrary angles remain seamless. At most two paths, independent of picture size.

`{pattern:crosshatch size=48, width=480, height=240/}`

Standard colors, opacity, title and SVG/data-URI options apply. Geometry is deterministic and seed-independent. No per-pixel or per-area shape growth.
