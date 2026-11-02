<?php

  // Records matched by a unique key, with added, removed and changed fields.
  return padDiffStructuredHtml ( padDiffRowChanges ( padDiffValue ( $padDiffOld ), padDiffValue ( $padDiffNew ), padTagParm ( 'key' ), (bool) padTagParm ( 'same', FALSE ) ), $padDiffFrom, $padDiffTo, 'rows' );

?>
