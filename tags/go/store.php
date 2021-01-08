<?php

  pad_trace ("store/start", "$pad_tag/$pad_walk: " . ($pad_parms_val [1] ?? ''));

  if ( pad_start_to_end () )
    return TRUE;

  if ( isset ( $pad_parms_val [1] ) ) {

    $pad_get_data = $pad_parms_val [1];
    $pad_get_prm = $pad_parms_val;

    unset ( $pad_get_prm [0] );
    unset ( $pad_get_prm [1] );

  } else  {

    $pad_get_data = $pad_html;
    $pad_get_prm = [];

  }

  $pad_get_store    = "pad_$pad_tag" . "_store";
  $pad_get_function = "pad_get_$pad_tag";

  $GLOBALS [$pad_get_store] [$pad_parm] = $pad_get_function ( $pad_get_data, pad_tag_parm('type'), $pad_get_prm ); 

  pad_trace ("store/end", "$pad_get_store / $pad_parm / $pad_get_function / $pad_get_data" );

  return NULL;

?>