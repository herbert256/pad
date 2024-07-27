<?php

  include_once pad . 'inits/levelVars.php';

  $padSolPad = $padSolApp = [];

  foreach ( $GLOBALS as $k => $v )
    if ( str_starts_with ( $k, 'pad') ) {
      $padSolPad [$k] = $GLOBALS [$k];
      global $$k;
    }

  foreach ( $GLOBALS as $k => $v )
    if ( padValidStore ($k) {
      $padSolApp [$k] = $GLOBALS [$k];
      unset ( $GLOBALS [$k] );
    }

  include pad . 'inits/level.php';

  for ( $i=0; $i <$pad ; $i++ )
    foreach ( $padLevelVars as $l )
      if ( isset ( $GLOBALS [$l] ) and is_array ($GLOBALS [$l]) and isset ( $GLOBALS [$l] [$i] ) ) 
        if ( is_array ( $GLOBALS [$l] [$i] ) )
          $GLOBALS [$l] [$i] = [];
        else
          $GLOBALS [$l] [$i] = '';

  foreach ( $padSetLvl [$pad] as $k => $v ) {
     $GLOBALS [$k] = $v;
     global $$k;
   }

  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = $padSeqStore = [];
   
  if ( isset ( $padSqlConnect ) )
    unset ( $padSqlConnect );

  $padBase [$pad] = $padSol;    

  include pad . 'occurrence/start.php'; 
  include pad . 'start/lib/level.php'; 

  $padSolReturn = $padPad [$pad+1];
 
  foreach ( $GLOBALS as $k => $v ) {
    if ( str_starts_with ( $k, 'pad') and ! in_array ($k, $padSolPad )  
      unset ( $GLOBALS [$k] );
 
  foreach ( $padSolPad as $k => $v ) 
    $GLOBALS [$k] = $padSolv;

  foreach ( $GLOBALS as $k => $v ) 
    if ( padValidStore ($k) 
      unset ( $GLOBALS [$k] );

  foreach ( $padSolApp as $k => $v ) 
    $GLOBALS [$k] = $padSolv;
 
  for ( $i=0; $i <=$pad ; $i++) { 
    reset ( $padData [$i] );
    while ( current ( $padData [$i] ) !== false and key ( $padData [$i] ) <> $padKey [$i] )
      next ( $padData [$i] );
  }

  return $padSolReturn;

?>