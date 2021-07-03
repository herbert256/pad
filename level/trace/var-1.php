<?php

  $pad_fld_cnt++;

  pad_trace ("field/start", "nr=$pad_fld_cnt " . '{' . $pad_between . '}');

  $pad_fld_trace = str_replace(':', '_', $pad_fld);
  $pad_fld_trace = str_replace('#', '_', $pad_fld_trace);  
  $pad_fld_trace = str_replace('$', '_', $pad_fld_trace);  

  if ( ! pad_valid_name ( $pad_fld_trace ) )
    $pad_fld_trace = 'not_valid';

  if ( ! pad_field_check ( $pad_fld ) ) {

    $pad_fld_trace = 'not_found';
 
    pad_trace ("field/error", "nr=$pad_fld_cnt *** NOT FOUND ***");
 
    $pad_trace_json = pad_json ( 
      [ 
        'field'  => $pad_fld,
        'nr'     => $pad_fld_cnt,
        'start'  => $pad_between,
        'error'  => "*** NOT FOUND ***"
      ] );

    pad_file_put_contents ( "errors/$app/$page/$PADREQID/field.$pad_fld_cnt.json", $pad_trace_json ); 
    pad_file_put_contents ( "$pad_trace_dir_base/errors/fields/$pad_fld_cnt.json", $pad_trace_json ); 
 
  }

?>