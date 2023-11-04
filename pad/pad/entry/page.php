<?php

  $padPagePage [$pad] = $padPage;
  $padPageDir  [$pad] = $padDir;
  $padPagePath [$pad] = $padPath;
 
  $padPage = padPageGetName ();
  $padDir  = padDir ();  
  $padPath = padPath ();  

  if ( $padTraceActive )
    include pad . 'trace/entry/page.php';

  if ( padTagParm ( 'include' ) )
    $padInclude = TRUE;

  $padPageType   = padTagParm ( 'type', 'include' );  
  $padPageResult = include pad . "pad/types/$padPageType.php";

  if ( $padTraceActive )
    include pad . 'trace/exit/page.php';
  
  $padPage = $padPagePage [$pad];
  $padDir  = $padPageDir  [$pad];
  $padPath = $padPagePath [$pad];

  unset ( $padInclude );

  return $padPageResult;

?>