<?php
  
  $padCatchFile  = $padCatchException->getFile();
  $padCatchLine  = $padCatchException->getLine();
  $padCatchError = $padCatchException->getMessage();
  $padCatchText  = "$padCatchFile:$padCatchLine $padCatchError" ;

  echo $padCatchText;
  exit;

?>