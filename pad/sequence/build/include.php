<?php

  if ( file_exists ( "sequence/types/$pqSeq/bool.php") ) 
    include_once "sequence/types/$pqSeq/bool.php";

  if ( file_exists ( "sequence/types/$pqSeq/check.php") ) 
    include_once "sequence/types/$pqSeq/check.php";
 
  if ( file_exists ( "sequence/types/$pqSeq/function.php") ) 
    include_once "sequence/types/$pqSeq/function.php";

  if ( file_exists ( "sequence/types/$pqSeq/generated.php") ) 
    include_once "sequence/types/$pqSeq/generated.php";

?>