<?php
  
  $padPrmsTag [$pad] = [];
  $padPrmsVal [$pad] = [];

  if ( ! in_array ( $padTag [$pad], ['if', 'case', 'while', 'until'] )  ) {
   
    $padPrmsOrg = padExplode ($padPrms [$pad], ',');
    
    foreach ( $padPrmsOrg as $padV ) {

      if ( $padV == 'trace' )
        include 'trace/option.php';

      $padW = padExplode ($padV, '=', 2);

      if ( count($padW) == 2 and substr($padW[0], 0, 1) == '$') {
        $padSetName  = trim(substr($padW[0], 1));
        $padSetValue = $padW[1];
        include 'set.php';
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