<?php

  // {dump} stops the request right there and shows the debug dump of the engine's state -
  // levels, variables, stores, request - as it stands at that point in the template.
  // padDump() ends with padExit(), so nothing after the tag is processed and it never
  // returns.

  padDump ( '{dump} tag used' );

?>