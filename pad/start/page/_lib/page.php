<?php

  if ( padXref ) 
    include pad . 'info/types/xref/events/page.php';

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