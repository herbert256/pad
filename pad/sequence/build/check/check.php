<?php

  if     ( file_exists ( "sequence/types/$pqSeq/bool.php"      ) ) $pqInfo ['build/check'] [] = 'bool';
  elseif ( file_exists ( "sequence/types/$pqSeq/fixed.php"     ) ) $pqInfo ['build/check'] [] = 'fixed';
  elseif ( file_exists ( "sequence/types/$pqSeq/build.php"     ) ) $pqInfo ['build/check'] [] = 'build';
  elseif ( file_exists ( "sequence/types/$pqSeq/generated.php" ) ) $pqInfo ['build/check'] [] = 'generated';
  else                                                             $pqInfo ['build/check'] [] = 'find';


  if     ( file_exists ( "sequence/types/$pqSeq/bool.php"      ) ) include "sequence/build/check/bool.php";
  elseif ( file_exists ( "sequence/types/$pqSeq/fixed.php"     ) ) include "sequence/build/check/fixed.php";
  elseif ( file_exists ( "sequence/types/$pqSeq/build.php"     ) ) include "sequence/build/check/build.php";
  elseif ( file_exists ( "sequence/types/$pqSeq/generated.php" ) ) include "sequence/build/check/generated.php";
  else                                                             include "sequence/build/check/find.php";

  if ( in_array ( $pqLoop, $pqTmp ) )
    return TRUE;
  else
    return FALSE;

?>