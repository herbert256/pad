<?php

  if ( padXref ) 
    include pad . 'info/types/xref/items/page.php';

  $padPageSavePage [$pad] = $padPage;
  $padPageSaveDir  [$pad] = $padDir;
  $padPageSavePath [$pad] = $padPath;
 
  $padPage = $padPagePage;
  $padDir  = padDir ();  
  $padPath = padPath ();  

  if ( $padPageInclude )
    $padInclude = TRUE;

  $padPageResult = include pad . "start/types/$padPageType.php";
  
  $padPage = $padPageSavePage [$pad];
  $padDir  = $padPageSaveDir  [$pad];
  $padPath = $padPageSavePath [$pad];

  unset ( $padInclude );

  return $padPageResult;

?>