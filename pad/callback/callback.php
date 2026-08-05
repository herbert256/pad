<?php

  // Runs the app callback named by the current tag's callback= parameter.
  //
  // padCallBackCheck resolves the name against _callbacks/ up the directory chain and
  // call/any.php executes it. The three wrappers in this directory set $padCallback to
  // the phase - init, row or exit - so a single callback file can serve all three.
  // Callbacks work by changing globals (the row in $row above all), not by returning
  // content: whatever they output here is discarded.

  $padCall = padCallBackCheck ( $padPrm [$pad] ['callback'] );

  include PAD . 'call/any.php';

?>