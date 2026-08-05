<?php

  // Snapshots the four named stores that outlive a level - the data, content and bool stores
  // plus the sequence store - into $padStrStoDat[$padStrCnt] before an isolated nested pass,
  // for start/end/stores.php to restore. They are listed in the padStrSto constant and are
  // deliberately kept out of the pad-globals snapshot, so that reset can clear them without
  // touching anything else.

  global $padStrStoDat;

  $padStrStoDat [$padStrCnt] = [];

  foreach ( padStrSto as $padStrVal )
    if ( isset ( $GLOBALS [$padStrVal] ))
      $padStrStoDat [$padStrCnt] [$padStrVal] = $GLOBALS [$padStrVal];

?>