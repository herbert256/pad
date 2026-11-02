<?php

  // Changed words marked as escaped deletions and insertions.
  return padTagParm ( 'side', FALSE ) || padTagParm ( 'lines', FALSE )
    ? padDiffLines ( $padDiffOld, $padDiffNew, (bool) padTagParm ( 'side', FALSE ), $padDiffFrom, $padDiffTo )
    : padDiffWords ( $padDiffOld, $padDiffNew );

?>
