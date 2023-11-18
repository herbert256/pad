<?php

  if ( $padXref ) 
    include pad . 'xref/page.php';

  $padPageSavePage [$pad] = $padPage;
  $padPageSaveDir  [$pad] = $padDir;
  $padPageSavePath [$pad] = $padPath;
 
  $padPage = $padPagePage;
  $padDir  = padDir ();  
  $padPath = padPath ();  

  if ( $padPageInclude )
    $padInclude = TRUE;

  $padStartType = "$padPageStart-$padPageType-$padPagePage";

  $padPageResult = include pad . "start/types/$padPageType.php";
  
  $padPage = $padPageSavePage [$pad];
  $padDir  = $padPageSaveDir  [$pad];
  $padPath = $padPageSavePath [$pad];

  unset ( $padInclude );

  return $padPageResult;

?>