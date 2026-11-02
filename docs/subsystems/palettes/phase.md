# phase

Cyclic angle scale; samples stop before the repeated first color. PAD original, GPL-3.0. Interpolation joins the final rose-violet anchor back to the first; no discontinuity at integer turns. No claim of perceptual uniformity.

Use `padPalette('phase', 9)` or `{widget:palette 'phase', steps=9/}`. Charts accept `palette='phase'`. One step returns the first color; cyclic steps never duplicate the endpoint.

The colors also work in print; do not rely on color alone for categories or values. Inspect grayscale using the regression luminance measurements.
