<?php

  if ( pad_tag_parm ('data') )
    $pad_tag_result = include PAD . "options/data.php";   

  if ( $pad_null )

    $pad_data [$pad_lvl] = [];

  elseif ( $pad_else )

    if ( count ($pad_data [$pad_lvl]) )
      $pad_data [$pad_lvl] = array_slice ($pad_data [$pad_lvl], 0, 1); 
    else
      $pad_data [$pad_lvl] = pad_default_data ();  

  elseif ( is_array ( $pad_tag_result ) )

    $pad_data [$pad_lvl] = $pad_tag_result;

  $pad_data [$pad_lvl] = pad_make_data ( $pad_data [$pad_lvl] );   

?>