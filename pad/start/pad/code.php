<?php

  // Supplies the base for a nested pass whose source is a string of PAD: puts $padStrCod
  // into the fresh level and starts iterating it. Reached from start/pad/pad.php when
  // $padStrBld is 'code', which is what {code}, {sandbox}, padCode() and padSandbox() use.

  $padBase [$pad] = $padStrCod;

  include PAD . 'occurrence/occurrence.php';

?>