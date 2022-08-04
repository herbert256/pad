<?php

  $pad--;

  $pad_data [$pad] = $pad_data [$pad+1];
  reset ( $pad_data[ $pad] );
  $pad_key [$pad] = key($pad_data[$pad]);

  $pad_parent_start = strpos ( $pad_base[$pad], '{'.$pad_tag );
  $pad_parent_end   = strpos ( $pad_base[$pad], "}", $pad_parent_start) ;

  $pad_base [$pad] = substr ( $pad_base[$pad], 0, $pad_parent_start )
                       . substr ( $pad_base[$pad], $pad_parent_end + 1 );

  $pad_current [$pad] = [];
  $pad_occur   [$pad] = 0;
  $pad_result  [$pad] = '';
  $pad_html    [$pad] = '';

?>