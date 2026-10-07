<?php

  // An {input} item that is an expression with an array value adds its keys as attributes,
  // as {attrs} does, so they are judged as {attrs} judges them: a key that is no attribute
  // name, an event handler and a URL that runs script are left out.

  $j = '{"x\" onmouseover=\"alert(7)":"y","onclick":"z","href":"javascript:alert(8)","title":"ok"}';

?>
