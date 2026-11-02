# Filter: gamma

`{pad:img 'photo.png', filter='gamma', gamma=2, alt='Photo'/}` applies `255*(channel/255)^(1/gamma)` independently to encoded sRGB R/G/B values; no linear-light conversion. Gamma > 1 lightens midtones, 1 is identity. Gamma must be finite, 0.01–100. Alpha is preserved exactly; transformation is identical at 1x/2x.

All PHP pixel loops declare `loops=true` so the existing two-megapixel budget applies. Options participate in thumbnail cache keys; chains run left to right. Run `php tests/subsystems/filter-gamma.php`.
