<?php

  // A value that calls a function by its bare name - strlen 5, no ( and no : - spliced into a
  // where= past the window the splice looks back over is refused as part of the condition, as
  // one with php:abs(-3) is: only a value with a (, a $ or a : in it was kept, and this one
  // ran PHP's strlen and let every row pass.

  $q = '3 or strlen 5 eq 1';

?>
