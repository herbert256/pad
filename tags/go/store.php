<?php

  pad_trace ("store", "$pad_tag/$pad_walk: " . ($pad_parms_val [1] ?? ''), TRUE);

  if ( pad_start_to_end () )
    return TRUE;

  if ( isset ( $pad_parms_val [1] ) and pad_get_check ( $pad_parms_val [1] ) ) {

    pad_trace ("store/get", $pad_parms_val [1], TRUE);

    $pad_get = $pad_parms_val [1];
    $pad_prm = $pad_parms_val;

    unset ( $pad_prm [0] );
    unset ( $pad_prm [1] );

    if     ( $pad_tag == 'content' ) $pad_content_store [$pad_parm] = pad_get_content ( $pad_get, $pad_prm ); 
    elseif ( $pad_tag == 'data'    ) $pad_data_store    [$pad_parm] = pad_get_data    ( $pad_get, $pad_prm, pad_tag_parm('type') ); 
    elseif ( $pad_tag == 'flag'    ) $pad_flag_store    [$pad_parm] = pad_get_flag    ( $pad_get, $pad_prm ); 

  } else  {

    pad_trace ("store/make", $pad_html [$pad_lvl], TRUE);

    if     ( $pad_tag == 'content' ) $pad_content_store [$pad_parm] = pad_make_string ( $pad_html [$pad_lvl] ); 
    elseif ( $pad_tag == 'data'    ) $pad_data_store    [$pad_parm] = pad_data        ( $pad_html [$pad_lvl], pad_tag_parm('type') );
    elseif ( $pad_tag == 'flag'    ) $pad_flag_store    [$pad_parm] = pad_make_flag   ( $pad_html [$pad_lvl] ); 

  }

  return NULL;

?>