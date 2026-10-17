<?php

  // The engine-side counterpart of start/end/unsetApp.php: unsets every pad/pq global that is
  // not in the snapshot start/start/pad.php took, so a sandboxed or cleaned pass cannot leave
  // new engine state behind either. The snapshotted ones have already been restored by
  // start/end/pad.php.

  // A key test, not in_array: the snapshot is keyed by name, and the loose value search
  // let any truthy engine value stand in for a name - the same defect unsetApp.php had.

  foreach ( array_diff_key ( padStrPadVars ( $GLOBALS ), $padStrZZZ [$padStrCnt] ) as $padStrKey => $padStrVal )
    unset ( $GLOBALS [$padStrKey] );

?>
