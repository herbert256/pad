<?php

  if ($pad_lvl > 1) 
    pad_data_chk ( $pad_data[$pad_lvl] );

  if ( isset ( $pad_parms_tag ['deDup'] ) )   
    include PAD_HOME . 'parms/dedup.php';

  if ( isset ( $pad_parms_tag ['sort'] ) )   
    include PAD_HOME . 'parms/sort.php';

  include PAD_HOME . 'parms/filter.php';
  
  if ( pad_tag_parm ('ignore') )
    $pad_base [$pad_lvl] = '{ignore}' . $pad_base [$pad_lvl] . '{/ignore}';    

  if ( pad_tag_parm ('source') )
    $pad_base [$pad_lvl] = '{ignore}' . pad_colors_string ($pad_base [$pad_lvl]) . '{/ignore}';  

?>