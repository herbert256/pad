# Filter: emboss

`{image:thumbnail 'photo.png', filter='emboss', strength=1, alt='Relief'/}` applies the signed kernel [-2,-1,0; -1,0,1; 0,1,2] to encoded luminance, divides by four, multiplies strength and adds neutral gray 128. Strength 0–10; zero yields a flat gray relief. Edges repeat the nearest pixel. Neighbors are one CSS pixel away at each density, with interpolation for fractional densities. Neighbor transparency blends toward the center luminance to avoid black fringes. Each center pixel's alpha is preserved; a separate output image avoids feedback. Lossless output by default.

All PHP pixel loops declare `loops=true` so the existing two-megapixel budget applies. Options participate in thumbnail cache keys; chains run left to right. Run `php tests/subsystems/filter-emboss.php`.
