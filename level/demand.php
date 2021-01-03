<?php

  if ( $pad_parms_type == 'close' )
    $pad_demand = ( isset ($pad_parms_tag['occurrence']) ) ? 'occur_end'   : 'level_end';
  else
    $pad_demand = ( isset ($pad_parms_tag['occurrence']) ) ? 'occur_start' : 'level_start';

  $pad_demand_level = ( $pad_parms_type == 'close' ) ? 'level_end' : 'level_start';
  $pad_demand_occur = ( $pad_parms_type == 'close' ) ? 'occur_end' : 'occur_start';  

?>