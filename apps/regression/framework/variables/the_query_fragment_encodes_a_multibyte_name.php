<?php

  // {?name} writes the name url-encoded as well as the value: a name with a multibyte letter
  // went into the link as written, &café=x+y, where the query string takes it as caf%C3%A9.

  $café = 'x y';

?>
