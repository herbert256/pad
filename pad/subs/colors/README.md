# Color computation

`padColor(kind, color, second = null, options = [])` provides `convert`, `mix`,
`contrast` and `gamut`. Input is `#rgb`, `#rgba`, `#rrggbb`, `#rrggbbaa`, or
`['space'=>'srgb','channels'=>[r,g,b],'alpha'=>1]`. Channel records also accept
`linear-srgb` and `hsl`. RGB channels and alpha use 0..1; HSL uses degrees and
0..1 saturation/lightness. Achromatic output hue is zero. Finite channel magnitudes
are bounded to 10,000; hue is wrapped. No CSS parser, named colors or external I/O.

* `convert`: one color, `to='srgb'|'linear-srgb'|'hsl'`. Returns space, channels,
  alpha, and an RGB hex string for sRGB (including alpha when below one).
* `mix`: two colors, `amount=.5` in [0,1], `space='srgb'|'linear-srgb'`.
  Interpolation premultiplies alpha; zero-alpha channels become zero. Returns sRGB.
* `contrast`: foreground and background, source-over compositing in encoded sRGB.
  A translucent background requires an explicitly supplied opaque `background`
  option. Returns ratio, effective colors and method, not a compliance verdict.
* `gamut`: accepts extended RGB channels; `method='check'` returns `inGamut` and a
  color only when in gamut. `method='clip'` independently clips channels to [0,1].
  Clipping is explicit and is not perceptual gamut mapping.

Transfer/HSL equations follow [CSS Color 4](https://www.w3.org/TR/css-color-4/).
Contrast follows [WCAG 2 relative luminance](https://www.w3.org/TR/WCAG22/#dfn-relative-luminance).
Existing palette and avatar luminance helpers delegate to this kernel; palette
interpolation remains unchanged. The older avatar threshold and current threshold
select the same branch for all 8-bit channels. Tests cover reference colors,
alpha, all grey values and a sampled cube against both old formulas.
