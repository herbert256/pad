<?php

  $pad_sort_args = [];

  $pad_sort_fields = pad_explode($pad_parms_pad ['sort'], ',');

  foreach ($pad_sort_fields as $pad_k => $pad_v) {

    $pad_sort_sort = '';
    $pad_sort_flags = 0; 

    $pad_sort_parms = pad_explode($pad_v, ' ');

    foreach($pad_sort_parms as $pad_k2 => $pad_v2) {
      if ($pad_k2==0)
        $pad_sort_field = $pad_v2;
      elseif (strtolower($pad_v2) == 'asc')
        $pad_sort_sort = 'ASC';
      elseif (strtolower($pad_v2) == 'desc')
        $pad_sort_sort = 'DESC';
      else 
        $pad_sort_flags = $pad_sort_flags | constant("SORT_" . strtoupper($pad_v2) );
    }

    $pad_sort_args [] = array_column ($pad_data[$pad_lvl], $pad_sort_field);

    if ($pad_sort_sort)
      $pad_sort_args [] = constant("SORT_$pad_sort_sort");

    if ($pad_sort_flags)
      $pad_sort_args [] = $pad_sort_flags;

  }

  $pad_sort_args [] = &$pad_data[$pad_lvl];

  call_user_func_array ('array_multisort', $pad_sort_args);
    
?>