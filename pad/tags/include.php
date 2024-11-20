<?php
  
  $padIncPage = padTagParm      ( 'include', $padParm );
  $padIncPage = padInclFileName ($padIncPage);
  $padIncPage = str_replace     (APP, '',  $padIncPage);
  
  return include 'tags/go/include.php';
  
?>