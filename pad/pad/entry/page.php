<?php

  $padPagePage [$pad] = $padPage;
  $padPageDir  [$pad] = $padDir;
  $padPagePath [$pad] = $padPath;
 
  $padPage = padPageGetName ();
  $padDir  = padDir ();  
  $padPath = padPath ();  

  if ( padTagParm ( 'include' ) )
    $padInclude = TRUE;

  $padPageType  = padTagParm ( 'type', 'include' );  
  $padEntryType = "page-$padPageType-$padPage";

  $padPageResult = include pad . "pad/types/$padPageType.php";
  
  $padPage = $padPagePage [$pad];
  $padDir  = $padPageDir  [$pad];
  $padPath = $padPagePath [$pad];

  unset ( $padInclude );

  return $padPageResult;

?>