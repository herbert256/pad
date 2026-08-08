<?php

  // The function group: look the path $names up in the local variables the PHP behind
  // level $padIdx left behind - a custom tag, function or callback file, captured into
  // $padLvlFunVar by level/function.php.

  // A level whose PHP left nothing behind has no entry, and the group walk asks about
  // every level - a missing one is a miss.

  global $padLvlFunVar;

  return padAtSearch ( $padLvlFunVar [$padIdx] ?? [], $names );

?>