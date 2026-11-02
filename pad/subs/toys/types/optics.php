<?php

  // optics - Trace rays through a movable light source, lenses and a plane mirror on an interactive optics bench.
  //

return padToy ( 'optics', 'Ray optics bench',['focal'=>padToyNumber('focal',100,40,240),'mirror'=>padToyFlag('mirror'),'second'=>padToyFlag('second')]);
?>
