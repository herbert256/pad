<?php

  // Makes a sandboxed or cleaned pass leave no trace on the application side: unsets every
  // application variable that is not in the snapshot start/start/app.php took, i.e. every one
  // the pass created. The ones that were already there are restored right after, by
  // start/end/app.php.

  // The snapshot is keyed by name, so membership is a key test. in_array searched the
  // values, and searched them loosely: a page variable holding TRUE matched every name the
  // pass invented, and {code sandbox} on such a page unset nothing at all.

  foreach ( array_diff_key ( padAppVars ( $GLOBALS ), $padStrApp [$padStrCnt] ) as $padStrKey => $padStrVal )
    unset ( $GLOBALS [$padStrKey] );

?>
