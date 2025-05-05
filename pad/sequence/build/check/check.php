<?php

  if     ( file_exists ( "sequence/types/$pqSeq/bool.php"      ) ) include "sequence/build/check/bool.php";
  elseif ( file_exists ( "sequence/types/$pqSeq/fixed.php"     ) ) include "sequence/build/check/fixed.php";
  elseif ( file_exists ( "sequence/types/$pqSeq/build.php"     ) ) include "sequence/build/check/build.php";
  elseif ( file_exists ( "sequence/types/$pqSeq/generated.php" ) ) include "sequence/build/check/generated.php";
  else                                                             include "sequence/build/check/find.php";

  return in_array ( $pqLoop, $pqTmp );

?>