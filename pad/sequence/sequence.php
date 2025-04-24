<?php

  include 'sequence/inits/inits.php';
  include 'sequence/build/build.php'; 
  include 'sequence/exits/exits.php';

  if   ( count ( $padData [$pad] ) ) return TRUE; 
  else                               return FALSE;

?>