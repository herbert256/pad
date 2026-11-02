<?php

  // Structural JSON changes by escaped JSON Pointer path, preserving types.
  return padDiffStructuredHtml ( padDiffJsonChanges ( padDiffValue ( $padDiffOld ), padDiffValue ( $padDiffNew ), (bool) padTagParm ( 'same', FALSE ) ), $padDiffFrom, $padDiffTo, 'json' );

?>
