<?php

  $pad_demand_level = ( $pad_parms_type == 'close' ) ? 'level_end' : 'level_start';
  $pad_demand_occur = ( $pad_parms_type == 'close' ) ? 'occur_end' : 'occur_start';  

  $pad_demand = ( isset ($pad_parms_tag ['occurrence']) ) ? $pad_demand_occur : $pad_demand_level;

?>