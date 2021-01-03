<?php

  pad_trace ( "xxx", $pad_walk);

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    if ( isset ( $pad_parms_val [1] ) and $pad_content )
      return pad_error ("A second parm and content are not both allowed");
    if ( ! isset ( $pad_parms_val [1] ) and ! $pad_content )
      return pad_error ("Or a second parm or content must be given");
  }

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
 
    $pad_walk = 'end';

  } elseif ( $pad_walk == 'start' ) {
      
    $pad_walk = 'occurrence-start';

  } else {

    if ( isset ( $pad_parms_val [1] ) and pad_get_check ( $pad_parms_val [1] ) ) {

      $pad_get = $pad_parms_val [1];
      $pad_prm = $pad_parms_val;

      unset ( $pad_prm [0] );
      unset ( $pad_prm [1] );

      if     ( $pad_tag == 'content' ) $pad_content_store [$pad_parm] = pad_get_content ( $pad_get, $pad_prm ); 
      elseif ( $pad_tag == 'data'    ) $pad_data_store    [$pad_parm] = pad_get_data    ( $pad_get, $pad_prm, pad_tag_parm('type') ); 
      elseif ( $pad_tag == 'flag'    ) $pad_flag_store    [$pad_parm] = pad_get_flah    ( $pad_get, $pad_prm ); 

    } else  {

      if     ( $pad_tag == 'content' ) $pad_content_store [$pad_parm] = pad_make_string ( $pad_html [$pad_lvl] ); 
      elseif ( $pad_tag == 'data'    ) $pad_data_store    [$pad_parm] = pad_data        ( $pad_html [$pad_lvl], pad_tag_parm('type') );
      elseif ( $pad_tag == 'flag'    ) $pad_flag_store    [$pad_parm] = pad_make_flag   ( $pad_html [$pad_lvl] ); 

    }

    $pad_html [$pad_lvl] = '';
    $pad_data [$pad_lvl] = [];

  }

  return TRUE;

?>