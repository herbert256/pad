<?php
  
  $pad_lvl--;

  $pad_data [$pad_lvl] = $pad_data [$pad_lvl+1];
  reset ( $pad_data[$pad_lvl] );
  $pad_key [$pad_lvl] = key($pad_data[$pad_lvl]);

  $pad_base  [$pad_lvl] = substr ( $pad_html[$pad_lvl], 0, $pad_start [$pad_lvl]  )
                        . substr ( $pad_html[$pad_lvl], $pad_end [$pad_lvl] + 1 );

  $pad_current [$pad_lvl] = [];
  $pad_occur   [$pad_lvl] = 0;
  $pad_result  [$pad_lvl] = '';
  $pad_html    [$pad_lvl] = '';

?>