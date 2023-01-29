<?php
  
  $padPrmCnt = 0;

  if ( ! in_array ( $padTag [$pad], ['if', 'case', 'while', 'until'] )  ) {
   
    $padPrmTmp = padParseOptions ( $padPrm [$pad] [0] );
    
    foreach ( $padPrmTmp as $padV ) {

      if ( $padV == 'trace' )
        include 'trace/option.php';

      $padPrmCnt++; 

      $padW = padExplode ($padV, '=', 2);

      if ( count($padW) == 2 and substr($padW[0], 0, 1) == '$') {
        $padSetName  = trim(substr($padW[0], 1));
        $padSetValue = $padW[1];
        include 'set.php';
        $padPrm [$pad] [$padPrmCnt]  = $GLOBALS [$padSetName];
        $padPrm [$pad] [$padSetName] = $GLOBALS [$padSetName];
        continue;
      } 

      if ( padValid ($padW[0]) and ! is_numeric($padW[0]) ) {
        if ( count($padW) == 1 ) $padPrm [$pad] [$padW[0]] = TRUE;
        else                     $padPrm [$pad] [$padW[0]] = padEval ( $padW[1] );
        $padPrm [$pad] [$padPrmCnt] = $padPrm [$pad] [$padW[0]]; 
      }
      else
        $padPrm [$pad] [$padPrmCnt] = padEval ( $padV );
  
    }
 
  }

  if ( ! isset ( $padPrm [$pad] [1] ) )
    $padPrm [$pad] [1]  = '';

?>