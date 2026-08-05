<?php

  // The function group: look the path $names up in the local variables the PHP behind
  // level $padIdx left behind - a custom tag, function or callback file, captured into
  // $padLvlFunVar by level/function.php.

  global $padLvlFunVar;

  return padAtSearch ( $padLvlFunVar  [$padIdx], $names );

?>