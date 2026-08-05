<?php

  // The application half of the blank slate: unsets every application variable that
  // start/start/app.php just snapshotted, so a sandboxed or reset pass cannot see the data
  // the calling page's PHP built up. start/end/app.php puts them all back afterwards.

  foreach ( $padStrApp [$padStrCnt] as $padStrKey => $padStrVal )
    unset ( $GLOBALS [$padStrKey] );

?>