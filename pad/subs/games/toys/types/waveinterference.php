<?php
// waveinterference - coherent point sources on a 10 metre plane, amplitude superposition.
return padToy('waveinterference','Wave interference',[
 'frequency'=>padToyNumber('frequency',1,.2,3),
 'phase'=>padToyNumber('phase',0,0,360),
 'separation'=>padToyNumber('separation',3,0,8)]);
