<?php

  if ( $pad_null )

    $pad_now = [];

  elseif ( $pad_else )

    if     ( $pad_array                   ) $pad_now = array_slice ($pad_tag_result, 0, 1); 
    elseif ( count ($pad_data [$pad]) ) $pad_now = array_slice ($pad_data [$pad], 0, 1); 
    else                                    $pad_now = pad_default_data ();  

  elseif ( $pad_array )

    $pad_now = $pad_tag_result;

  else 

    $pad_now = $pad_data [$pad];

  $pad_data [$pad] = pad_make_data ( $pad_now );   

?>