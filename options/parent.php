<?php

  $pad_lvl--;

  $pad_data [$pad_lvl] = $pad_data [$pad_lvl+1];
  reset ( $pad_data[ $pad_lvl] );
  $pad_key [$pad_lvl] = key($pad_data[$pad_lvl]);

  $pad_parent_start = strpos ( $pad_base[$pad_lvl], '{'.$pad_tag );
  $pad_parent_end   = strpos ( $pad_base[$pad_lvl], "}", $pad_parent_start) ;

  $pad_base [$pad_lvl] = substr ( $pad_base[$pad_lvl], 0, $pad_parent_start )
                       . substr ( $pad_base[$pad_lvl], $pad_parent_end + 1 );

  $pad_current [$pad_lvl] = [];
  $pad_occur   [$pad_lvl] = 0;
  $pad_result  [$pad_lvl] = '';
  $pad_html    [$pad_lvl] = '';

?>