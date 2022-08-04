<?php

  if ( $pad_tag == 'level')
    return pad_arr_to_html ( $pad_data[$pad-1] );
  else
    return pad_arr_to_html ( $pad_data[$pad-1] [$pad_key[$pad-1]] );
 
  return $pad_return;

?>