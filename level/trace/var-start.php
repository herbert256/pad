<?php

  $pad_val_base = $pad_val;

  $pad_fld_trace = $pad_fld;

  if ( ! pad_valid_name ( $pad_fld_trace ) )
    $pad_fld_trace = 'not_valid';

  if ( ! pad_field_check ( $pad_fld ) ) {

    $pad_fld_trace = 'not_found';
 
    $pad_trace_json = pad_json ( 
      [ 
        'app'    => $app,
        'page'   => $page,
        'id'     => $PADREQID,
        'field'  => $pad_fld,
        'nr'     => $pad_fld_cnt,
        'start'  => $pad_between,
        'error'  => "*** NOT FOUND ***"
      ] 
    );

    pad_file_put_contents ( "errors/field/$app/$page/$PADREQID/$pad_fld_cnt.json", $pad_trace_json ); 
    pad_file_put_contents ( "$pad_trace_dir_base/errors/fields/$pad_fld_cnt.json", $pad_trace_json ); 
 
  }

?>