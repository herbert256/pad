<?php

  // Forwards to properties/option.php: the value of one named option of the target level,
  // named after the dot - option.sort@items. options.php returns them all at once.

  return include PAD . "properties/$name.php";

?>
