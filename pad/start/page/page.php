<?php

  $padPagePage    = $padParm;
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'get' );

  $padPageSavePage [$pad] = $padPage;
  $padPageSaveDir  [$pad] = $padDir;
  $padPageSavePath [$pad] = $padPath;
 
  $padPage = $padPagePage;
  $padDir  = padDir ();  
  $padPath = padPath ();  

  if ( $padPageInclude )
    $padInclude = TRUE;

  $padPageResult = include pad . "start/page/types/$padPageType.php";
  
  $padPage = $padPageSavePage [$pad];
  $padDir  = $padPageSaveDir  [$pad];
  $padPath = $padPageSavePath [$pad];

  unset ( $padInclude );

  return $padPageResult;

?>