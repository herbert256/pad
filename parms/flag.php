<?php

  if ( isset ( $GLOBALS ['pad_flag_store'] [pad_tag_parm('flag')] ) )
    $pad_flag_check = $GLOBALS ['pad_flag_store'] [pad_tag_parm('flag')];
  else
    $pad_flag_check = pad_get ( pad_tag_parm ('flag'), 'flag' )

  if ( $pad_flag_check =  === FALSE ) {

    $pad_parms = '';
    include PAD_HOME . 'level/parms.php';

    $pad_tag_type = 'null';

  }

?>