<?php

  // The engine-side counterpart of start/end/unsetApp.php: unsets every pad/pq global that is
  // not in the snapshot start/start/pad.php took, so a sandboxed or cleaned pass cannot leave
  // new engine state behind either. The snapshotted ones have already been restored by
  // start/end/pad.php.

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padStrPad ( $padStrKey ) )
      if ( ! in_array  ( $padStrKey, $padStrZZZ [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );

?>