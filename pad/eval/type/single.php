<?php

  // Dispatches a parameterless type to its eval/single/$kind.php handler and returns the value
  // that handler looks up for $name.

  return include PAD . "eval/single/$kind.php" ;

?>