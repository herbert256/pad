<?php
  
  $pPrmsTag [$pN] = [];
  $pPrmsVal [$pN] = [];

  if ( ! in_array ( $pTag [$pN], ['if', 'case', 'while', 'until'] )  ) {
   
    $pPrms_org = pExplode ($pPrms [$pN], ',');
    
    foreach ( $pPrms_org as $pV ) {

      if ( $pV == 'trace' )
        include 'trace/option.php';

      $pW = pExplode ($pV, '=', 2);

      if ( count($pW) == 2 and substr($pW[0], 0, 1) == '$') {
        $pSet_name  = trim(substr($pW[0], 1));
        $pSet_value = $pW[1];
        include PAD . 'level/set.php';
        continue;
      } 

      if ( pValid ($pW[0]) and ! is_numeric($pW[0]) )
        if ( count($pW) == 1 )
          $pPrmsTag [$pN] [$pW[0]] = TRUE;
        else
          $pPrmsTag [$pN] [$pW[0]] = pEval ( $pW[1] );
      else
        $pPrmsVal [$pN] [] = pEval ( $pV );

    }
 
  }

  $pPrm  [$pN] = $pPrmsVal [$pN][0] ?? '';
  $pName [$pN] = $pPrmsTag [$pN]['name'] ?? $pTag [$pN];

?>