<?php

  $padPrm [$pad] = [];
  $padPrmCnt     = 0;

  $padWords = preg_split("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  $padTag [$pad]     = trim($padWords[0] ?? '');
  $padPrm [$pad] [0] = trim($padWords[1] ?? '');
  
  if ( ! in_array ( $padTag [$pad], $padNoParmsParse  )  ) {
   
    $padPrmTmp = padParseOptions ( $padPrm [$pad] [0] );
    
    foreach ( $padPrmTmp as $padV ) {

      if ( $padV == 'trace' )
        include 'trace/option.php';

      $padW = padExplode ($padV, '=', 2);

      if ( count($padW) == 2 and substr($padW[0], 0, 1) == '$' )
        continue;

      $padPrmCnt++; 

      if ( padValid ($padW[0]) and ! is_numeric($padW[0]) ) {
   
        if ( count($padW) == 1 ) {
          $padPrm [$pad] [$padW[0]]   = TRUE;
          $padPrm [$pad] [$padPrmCnt] = $padW[0]; 
        }
        else {                    
          $padPrm [$pad] [$padW[0]]   = padEval ( $padW[1] );
          $padPrm [$pad] [$padPrmCnt] = $padPrm [$pad] [$padW[0]];
          if ( $padPrmCnt == 1 )
            $padPrm [$pad] ['_first_Key_'] = $padW[0];
        }
   
      }
   
      else
   
        $padPrm [$pad] [$padPrmCnt] = padEval ( $padV );
  
    }
 
  }

  if ( ! isset ( $padPrm [$pad] [1] ) )
    $padPrm [$pad] [1]  = '';

?>