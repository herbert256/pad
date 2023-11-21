<?php
  
  $padIncPage = padTagParm ( $padTag [$pad] );
  $padIncPage = padInclFileName ($padIncPage);
  $padIncPage = str_replace (padApp, '',  $padIncPage);
  
  dump();
  return "xxx$padIncPage--"; 
  return include pad . '_tags/go/include.php';

?>