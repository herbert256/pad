<?php
  
  $pPrmsTag [$p] = [];
  $pPrmsVal [$p] = [];

  if ( ! in_array ( $pTag??'', ['if', 'case', 'while', 'until'] )  ) {
   
    $pPrms_org = pExplode ($pPrms??'', ',');
    
    foreach ( $pPrms_org as $pad_v ) {

      if ( $pad_v == 'trace' )
        include 'trace/option.php';

      $pad_w = pExplode ($pad_v, '=', 2);

      if ( count($pad_w) == 2 and substr($pad_w[0], 0, 1) == '$') {
        $pSet_name  = trim(substr($pad_w[0], 1));
        $pSet_value = $pad_w[1];
        include PAD . 'level/set.php';
        continue;
      } 

      if ( pad_valid ($pad_w[0]) and ! is_numeric($pad_w[0]) )
        if ( count($pad_w) == 1 )
          $pPrmsTag [$p] [$pad_w[0]] = TRUE;
        else
          $pPrmsTag [$p] [$pad_w[0]] = pEval ( $pad_w[1] );
      else
        $pPrmsVal [$p] [] = pEval ( $pad_v );

    }
 
  }

  $pTag  [$p]     = $pTag;
  $pName [$p]    = $pPrmsTag [$p] ['name'] ?? $pTag [$p];
  $pName [$p]    = $pPrmsVal [$p] [0] ?? '';
  $pPrms [$p]    = $pPrms;
  $pPrmsTag [$p] = $pPrmsTag [$p];  
  $pPrmsVal [$p] = $pPrmsVal [$p];

?>