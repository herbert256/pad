<?php

  if ( $pad_walk == 'start' ) {
 
    $pad_range_range = pad_explode ($pad_parms_org [0], '..');

    if ( ! isset($pad_parms_tag ['walk']) )
      return range($pad_range_range[0], $pad_range_range[1], $pad_parms_tag['step']??1);

    $pad_range     [$pad_lvl] = range($pad_range_range[0], $pad_range_range[1], $pad_parms_tag['step']??1);
    $pad_range_cnt [$pad_lvl] = 0;

    $pad_walk = 'next';

  } else {

    $pad_range_cnt [$pad_lvl]++;

  }

  if ( $pad_range_cnt [$pad_lvl]+1 > count ( $pad_range [$pad_lvl] ) ) {  
    $pad_walk = '';
    return NULL;
  }

  return [ 0 => $pad_range [$pad_lvl] [ $pad_range_cnt [$pad_lvl] ] ];

?>