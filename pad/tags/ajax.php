<?php

  if ( ! isset($pad_ajax) )
    $pad_ajax = 0;
  
  $pad_ajax++;

  $pad_url = "{$pad}{$pad_parm}&pad_include=$pad_ajax";

  foreach($pad_parms_pad as $pad_pair_key => $pad_pair_value)
    if ( substr($pad_pair_key, 0, 4) <> 'pad_' and ! is_array($pad_pair_key) )
      if ( $pad_pair_key )
        $pad_url .= '&' . $pad_pair_key . '=' . urlencode($pad_pair_value);

  return TRUE;

?>