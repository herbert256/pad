<?php

  // Makes a sandboxed or cleaned pass leave no trace on the application side: unsets every
  // application variable that is not in the snapshot start/start/app.php took, i.e. every one
  // the pass created. The ones that were already there are restored right after, by
  // start/end/app.php.

  // The snapshot is keyed by name, so membership is a key test. in_array searched the
  // values, and searched them loosely: a page variable holding TRUE matched every name the
  // pass invented, and {code sandbox} on such a page unset nothing at all.

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ( $padStrKey ) )
      if ( ! array_key_exists ( $padStrKey, $padStrApp [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );

?>