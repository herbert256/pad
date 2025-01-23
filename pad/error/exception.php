<?php
  
  $GLOBALS ['padException'] = $padException;
  
  global $padExceptionFile, $padExceptionLine, $padExceptionError, $padExceptionText;

  $padExceptionFile  = $padException->getFile();
  $padExceptionLine  = $padException->getLine();
  $padExceptionError = $padException->getMessage();
  $padExceptionText  = "$padExceptionFile:$padExceptionLine $padExceptionError" ;

?>