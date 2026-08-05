<?php

  // Makes a sandboxed or cleaned pass leave no trace on the application side: unsets every
  // application variable that is not in the snapshot start/start/app.php took, i.e. every one
  // the pass created. The ones that were already there are restored right after, by
  // start/end/app.php.

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ( $padStrKey ) )
      if ( ! in_array  ( $padStrKey, $padStrApp [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );

?>