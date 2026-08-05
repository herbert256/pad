<?php

  // Stacks the six flags describing a nested pass - what to build, and how isolated it is -
  // under $padStrCnt, so start/end/end.php can restore them when the pass returns and the
  // enclosing pass finds its own settings again.
  //
  // Asking for clean and reset together means the pass should neither see nor leave anything,
  // which is exactly sandbox, so $padStrBox is turned on here rather than tested for
  // everywhere downstream.

  if ( $padStrCln and $padStrRes )
    $padStrBox = TRUE;

  $padStrStr [$padStrCnt] [0] = $padStrBld;
  $padStrStr [$padStrCnt] [1] = $padStrBox;
  $padStrStr [$padStrCnt] [2] = $padStrCln;
  $padStrStr [$padStrCnt] [3] = $padStrCod;
  $padStrStr [$padStrCnt] [4] = $padStrFun;
  $padStrStr [$padStrCnt] [5] = $padStrRes;

?>