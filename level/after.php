<?php

  if ($pad_lvl > 1) 
    pad_data_chk ( $pad_data[$pad_lvl] );

  if ( isset ( $pad_parms_pad ['sort'] ) )   
    include PAD_HOME . 'level/sort.php';

  include PAD_HOME . 'level/filter.php';
  
  if ( pad_tag_parm ('ignore') )
  $pad_base [$pad_lvl] = '{ignore}' . $pad_base [$pad_lvl] . '{/ignore}';    

  if ( pad_tag_parm ('source') )
    $pad_base [$pad_lvl] = '{ignore}' . pad_colors_string ($pad_base [$pad_lvl]) . '{/ignore}';  

?>