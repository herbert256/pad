<?php
  
  $padPrmsTag [$pad] = [];
  $padPrmsVal [$pad] = [];

  if ( ! in_array ( $padTag [$pad], ['if', 'case', 'while', 'until'] )  ) {
   
    $padPrms_org = padExplode ($padPrms [$pad], ',');
    
    foreach ( $padPrms_org as $padV ) {

      if ( $padV == 'trace' )
        include 'trace/option.php';

      $padW = padExplode ($padV, '=', 2);

      if ( count($padW) == 2 and substr($padW[0], 0, 1) == '$') {
        $padSet_name  = trim(substr($padW[0], 1));
        $padSet_value = $padW[1];
        include PAD . 'level/set.php';
        continue;
      } 

      if ( padValid ($padW[0]) and ! is_numeric($padW[0]) )
        if ( count($padW) == 1 )
          $padPrmsTag [$pad] [$padW[0]] = TRUE;
        else
          $padPrmsTag [$pad] [$padW[0]] = padEval ( $padW[1] );
      else
        $padPrmsVal [$pad] [] = padEval ( $padV );

    }
 
  }

  $padPrm  [$pad] = $padPrmsVal [$pad][0] ?? '';
  $padName [$pad] = $padPrmsTag [$pad]['name'] ?? $padTag [$pad];

?>