<?php

  if ( ! count ($pData[$pad] ) )
    return;

  $pSort_args   = [];
  $pSort_fields = pExplode($pPrms_tag ['sort'], ';'); 

  if ( $pPrms_tag ['sort'] === TRUE or ! count ($pSort_fields)) {
    $pSort_fields = []; 
    foreach ($pData[$pad] as $pad_v1) {
      foreach ($pad_v1 as $pK2 => $pad_v2) 
        $pSort_fields [] = $pK2;
      break;
    }
  }

  foreach ($pSort_fields as $pK => $pad_v) {

    $pSort_sort = '';
    $pSort_flags = 0; 

    $pSort_parms = pExplode($pad_v, ' ');

    foreach($pSort_parms as $pK2 => $pad_v2) {
      if ($pK2==0)
        $pSort_field = $pad_v2;
      elseif (strtolower($pad_v2) == 'asc')
        $pSort_sort = 'ASC';
      elseif (strtolower($pad_v2) == 'desc')
        $pSort_sort = 'DESC';
      else 
        $pSort_flags = $pSort_flags | constant("SORT_" . strtoupper($pad_v2) );
    }

    $pSort_args [] = array_column ($pData[$pad], $pSort_field);

    if ($pSort_sort)
      $pSort_args [] = constant("SORT_$pSort_sort");

    if ($pSort_flags)
      $pSort_args [] = $pSort_flags;

  }
 
  $pSort_args [] = &$pData[$pad];

  call_user_func_array ('array_multisort', $pSort_args);
    
?>