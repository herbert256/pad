<?php

  // Loads the helper files of sequence type $pqSeq so the build can call into them.
  //
  // Pulls in the type's bool.php, check.php, function.php and generated.php - each
  // optional, each include_once - which is where pqBoolXxx(), pqXxx() and the precomputed
  // PADxxx constant come from. Called by build/build.php and by plays/add.php.

  if ( file_exists ( PT . "$pqSeq/bool.php" )      ) include_once PT . "$pqSeq/bool.php";
  if ( file_exists ( PT . "$pqSeq/check.php" )     ) include_once PT . "$pqSeq/check.php";
  if ( file_exists ( PT . "$pqSeq/function.php" )  ) include_once PT . "$pqSeq/function.php";
  if ( file_exists ( PT . "$pqSeq/generated.php" ) ) include_once PT . "$pqSeq/generated.php";

?>