<?php

  // Hands the calling tag's own {set} variables to the nested pass, as globals. Run last in
  // start/pad/start.php, after the state has been saved and possibly wiped, so these are the
  // only application variables a sandboxed pass starts with - the documented way of passing
  // arguments into one. The global statement binds each name in the current scope too, which
  // matters when the pass is running inside the function scope of start/pad/function.php.

  foreach ( $padSetLvl [$pad] as $padStrKey => $padStrVal ) {
    $GLOBALS [$padStrKey] = $padStrVal;
    global $$padStrKey;
  }

?>