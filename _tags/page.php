<?php
  
  $padPageInsert [$pad] = $padPage;
  $padDirInsert  [$pad] = $padDir;
  $padPathInsert [$pad] = $padPath;
 
  $padPage = padPageGetName ();
  $padDir  = padDir ();  
  $padPath = padPath ();  

  if ( padTagParm ( 'include' ) )
    $padInclude = TRUE;

  $padPagTyp = padTagParm ( 'type', 'sandbox' );
  
  $padPageResult = include pad . "page/$padPagTyp.php";

  $padPage = $padPageInsert [$pad];
  $padDir  = $padDirInsert  [$pad];
  $padPath = $padPathInsert [$pad];

  unset ( $padInclude );
   
  return $padPageResult;

?>