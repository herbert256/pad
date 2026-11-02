# Filter: colormatrix

`{pad:img 'photo.png', filter='colormatrix', matrix='0,0,1,0,0, 0,1,0,0,0, 1,0,0,0,0, 0,0,0,1,0', alt='Red/blue swap'/}` applies a row-major 4×5 matrix to normalized unpremultiplied [R,G,B,A,1] in encoded sRGB. Exactly twenty finite coefficients in -10..10, as an array or comma/space-separated text. Default is identity. Each result channel clamps to 0..1; alpha is opacity (1 opaque), converted to GD's 7-bit transparency. Thus identity preserves alpha exactly; custom matrices can transform it. Matrix offsets are normalized values, not 0..255.

All PHP pixel loops declare `loops=true` so the existing two-megapixel budget applies. Options participate in thumbnail cache keys; chains run left to right. Run `php tests/subsystems/filter-colormatrix.php`.
