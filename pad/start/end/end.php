<?php

  // Restores the six pass flags that start/start/start.php stacked, so the rest of
  // start/pad/end.php can see how isolated the pass that is now finishing actually was, and
  // so the enclosing pass gets its own settings back.

  $padStrBld = $padStrStr [$padStrCnt] [0];
  $padStrBox = $padStrStr [$padStrCnt] [1];
  $padStrCln = $padStrStr [$padStrCnt] [2];
  $padStrCod = $padStrStr [$padStrCnt] [3];
  $padStrFun = $padStrStr [$padStrCnt] [4];
  $padStrRes = $padStrStr [$padStrCnt] [5];

?>