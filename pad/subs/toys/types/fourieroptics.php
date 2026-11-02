<?php

  // fourieroptics - A one-dimensional uniformly lit aperture and scalar coherent Fraunhofer far-field diffraction, computed with a bounded 128-point discrete Fourier transform (16,384 terms per control change). Slit width 10..200 μm, separation 20..600 μm, wavelength 380..780 nm, and one/two slits. Overlap forms a single union aperture. The fixed 1 mm aperture window and 1 m focal plane are explicit; this is a sampled educational model, not a vector electromagnetic solver. It is static until controls change, with no frame loop, so reduced-motion/hidden pages do no ongoing simulation work.
  //
  // `{toy:fourieroptics seed=17, paused/}`. Shared theme colours, CSP loading and responsive canvas apply.

return padToy('fourieroptics','Fourier optics',['slitwidth'=>padToyInt('slitwidth',80,10,200),'separation'=>padToyInt('separation',250,20,600),'wavelength'=>padToyInt('wavelength',550,380,780),'slits'=>padToyInt('slits',2,1,2)]);
