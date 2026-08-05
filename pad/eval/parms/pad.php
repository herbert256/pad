<?php

  // Calls a built-in pipe function from PAD/functions/, the handler for kind 'pad'.
  // The function file reads $value, $parm and $count from scope; its result is returned.

  return include PAD . "functions/$name.php";

?>