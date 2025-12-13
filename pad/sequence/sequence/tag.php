<?php

  include PQ . 'inits/tag.php';
  include PQ . 'inits/inits.php';
  include PQ . 'build/build.php'; 
  include PQ . 'exits/exits.php';

  if   ( count ( $padData [$pad] ) ) return TRUE; 
  else                               return FALSE;

?>