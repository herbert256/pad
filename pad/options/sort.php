<?php

  if ( ! count ($padData [$pad] ) )
    return;

  $padSort_args   = [];
  $padSort_fields = padExplode($padPrmsTag [$pad] ['sort'], ';'); 

  if ( $padPrmsTag [$pad] ['sort'] === TRUE or ! count ($padSort_fields)) {
    $padSort_fields = []; 
    foreach ($padData [$pad] as $padV1) {
      foreach ($padV1 as $padK2 => $padV2) 
        $padSort_fields [] = $padK2;
      break;
    }
  }

  foreach ($padSort_fields as $padK => $padV) {

    $padSort_sort = '';
    $padSort_flags = 0; 

    $padSort_parms = padExplode($padV, ' ');

    foreach($padSort_parms as $padK2 => $padV2) {
      if ($padK2==0)
        $padSort_field = $padV2;
      elseif (strtolower($padV2) == 'asc')
        $padSort_sort = 'ASC';
      elseif (strtolower($padV2) == 'desc')
        $padSort_sort = 'DESC';
      else 
        $padSort_flags = $padSort_flags | constant("SORT_" . strtoupper($padV2) );
    }

    $padSort_args [] = array_column ($padData [$pad], $padSort_field);

    if ($padSort_sort)
      $padSort_args [] = constant("SORT_$padSort_sort");

    if ($padSort_flags)
      $padSort_args [] = $padSort_flags;

  }
 
  $padSort_args [] = &$padData [$pad];

  call_user_func_array ('array_multisort', $padSort_args);
    
?>