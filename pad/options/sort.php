<?php

  if ( ! count ($pData[$p] ) )
    return;

  $pSort_args   = [];
  $pSort_fields = pExplode($pPrmsTag[$p] ['sort'], ';'); 

  if ( $pPrmsTag[$p] ['sort'] === TRUE or ! count ($pSort_fields)) {
    $pSort_fields = []; 
    foreach ($pData[$p] as $pV1) {
      foreach ($pV1 as $pK2 => $pV2) 
        $pSort_fields [] = $pK2;
      break;
    }
  }

  foreach ($pSort_fields as $pK => $pV) {

    $pSort_sort = '';
    $pSort_flags = 0; 

    $pSort_parms = pExplode($pV, ' ');

    foreach($pSort_parms as $pK2 => $pV2) {
      if ($pK2==0)
        $pSort_field = $pV2;
      elseif (strtolower($pV2) == 'asc')
        $pSort_sort = 'ASC';
      elseif (strtolower($pV2) == 'desc')
        $pSort_sort = 'DESC';
      else 
        $pSort_flags = $pSort_flags | constant("SORT_" . strtoupper($pV2) );
    }

    $pSort_args [] = array_column ($pData[$p], $pSort_field);

    if ($pSort_sort)
      $pSort_args [] = constant("SORT_$pSort_sort");

    if ($pSort_flags)
      $pSort_args [] = $pSort_flags;

  }
 
  $pSort_args [] = &$pData[$p];

  call_user_func_array ('array_multisort', $pSort_args);
    
?>