<?php

  // WHERE={$cond} is refused as where={$cond} is: a file system that ignores case finds the
  // where option for WHERE= as well, and the value spliced there ran as the condition.

  $cond = '$n eq php:abs(-3)';

?>
