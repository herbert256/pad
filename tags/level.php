<?php

  if ( $pad_tag == 'level')
    return pad_arr_to_html ( $pad_data[$pad_lvl-1] );
  else
    return pad_arr_to_html ( $pad_data[$pad_lvl-1] [$pad_key[$pad_lvl-1]] );
 
  return $pad_return;

?>