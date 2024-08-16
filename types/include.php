<?php
  
  $padIncPage = padTagParm ( $padTag [$pad] );
  $padIncPage = padInclFileName ($padIncPage);
  $padIncPage = str_replace ('/app/', '',  $padIncPage);
  
  return include '/pad/tags/go/include.php';

?>