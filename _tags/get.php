<?php
  
  $padPageInsert [$pad] = $padPage;
  $padDirInsert  [$pad] = $padDir;
  $padPathInsert [$pad] = $padPath;
 
  $padPage = padPageGetName ();
  $padDir  = padDir ();  
  $padPath = padPath ();  

  $padInclude = TRUE;
  
  $padBetween   = 'true';
  $padFirst     = 't';
  $padWords     = [];
  $padWords [0] = 'true';

  $padTypeCheck  = 'true';
  $padTypeResult = 'pad';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php';
  include pad . 'build/build.php';   

  $padPageLevel [] = $pad;
  while ( $pad >= end ( $padPageLevel ) ) 
    include pad . 'level/level.php'; 
  array_pop ( $padPageLevel );

  $padPage = $padPageInsert [$pad];
  $padDir  = $padDirInsert  [$pad];
  $padPath = $padPathInsert [$pad];

  unset ( $padInclude );
   
  return $padContent;

?>