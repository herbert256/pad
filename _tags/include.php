<?php
  
  $padIncPage = padTagParm ( 'include', $padOpt [$pad] [1] );
  $padIncPage = padInclFileName ($padIncPage);
  $padIncPage = str_replace (padApp, '',  $padIncPage);
  
  return include pad . '_tags/go/include.php';

?>