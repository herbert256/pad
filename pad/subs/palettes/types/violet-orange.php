<?php

  // violet-orange - Original GPL-3.0 diverging sRGB ramp. The meaningful center is exactly 0.5 (`#faf7f1`); both arms darken away from the center.
  //
  // Use `padPaletteSelect("violet-orange", "diverging", 9)` or `{widget:palette "violet-orange"/}`. The categorical selector rejects it. On light or dark surfaces use contrasting outlines and labels; the metadata reports mark contrast, not universal accessibility.

return ['name'=>'violet-orange','type'=>'diverging','source'=>'PAD original (GPL-3.0)','midpoint'=>0.5,'colors'=>['#49236b', '#765195', '#ac90c0', '#d8c8e2', '#faf7f1', '#f2d8b8', '#dba566', '#ad6c2f', '#704113']];
