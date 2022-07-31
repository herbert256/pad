<?php
  
  const pad_eval_precedence = [
    '!',
    'TYPE',
    'FUN',
    '**', '*', '/', '%', '+', '-',
    '.',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'AND', 'XOR', 'OR',
    'NOT',
  ];

  const pad_eval_1   = [ '!', '+', '-', '*', '/', '%', '.' ];
  const pad_eval_2   = [ '**'];
  const pad_eval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];
  
  const pad_eval_alt = [ 
    '<'  => 'LT', 
    '<=' => 'LE', 
    '>'  => 'GT', 
    '>=' => 'GE', 
    '='  => 'EQ', 
    '==' => 'EQ', 
    '<>' => 'NE'
  ];

  function pad_eval ($eval, $myself='') {

    if ( strlen(trim($eval)) == 0 )
      return '';

    pad_timing_start ('eval');

    global $pad_eval_cnt, $pad_eval_step_cnt, $pad_eval_start, $pad_eval_result, $pad_trace;

    $GLOBALS ['pad_trace_eval_stage']   = 'start';
    $GLOBALS ['pad_trace_eval_eval']    = $eval;
    $GLOBALS ['pad_trace_eval_myself']  = $myself;
    $GLOBALS ['pad_trace_eval_parsed']  = [];
    $GLOBALS ['pad_trace_eval_after']   = [];
    $GLOBALS ['pad_trace_eval_now']     = [];

    $pad_eval_cnt++;
    $pad_eval_step_cnt = 0;
    $pad_eval_start = $eval;
    $pad_eval_result = [];

    pad_eval_trace  ('start', [ 'eval' => $eval, 'myself' => $myself ] );

    $pad_eval_parse = pad_eval_parse ( $pad_eval_result, $eval, $myself );

    if ( $pad_eval_parse )
      return pad_eval_error ($pad_eval_parse);
    $GLOBALS ['pad_trace_eval_parsed'] = $pad_eval_result;

    pad_eval_trace  ('parse', $pad_eval_result );

    $pad_eval_after = pad_eval_after ( $pad_eval_result, $eval );  
    if ( $pad_eval_after )
      return pad_eval_error ($pad_eval_after);
    $GLOBALS ['pad_trace_eval_after'] = $pad_eval_result;

    pad_eval_trace  ('after', $pad_eval_result );

    pad_eval_go ( $pad_eval_result, array_key_first($pad_eval_result), array_key_last($pad_eval_result), $myself) ;

    $GLOBALS ['pad_trace_eval_go'] =  $pad_eval_result;

    $key = array_key_first ($pad_eval_result);
      
    if ( count($pad_eval_result) < 1 )
      return pad_eval_error("No result back");
    elseif ( count($pad_eval_result) > 1 )                                                
      return pad_eval_error("More then one result back");
    elseif ( isset($pad_eval_result[$key][4]) ) 
      return pad_eval_error("Result is an array");
    elseif ( $pad_eval_result[$key][1] <> 'VAL' )         
      return pad_eval_error("Result is not a value");

    pad_eval_trace  ('end', $pad_eval_result );

    $GLOBALS ['pad_trace_eval_stage'] = 'end';

    pad_timing_end ('eval');
 
    return $pad_eval_result [$key] [0];

  }


  function pad_eval_error ($txt) {

    pad_eval_trace  ('error', [ 'error' => $txt, 'result' => $GLOBALS ['pad_eval_result'] ] );

    $GLOBALS ['pad_trace_eval_stage'] = 'error';

    global $pad_eval_cnt;

    $data = [
      'eval'    => $GLOBALS ['pad_trace_eval_eval']   ?? '',
      'myself'  => $GLOBALS ['pad_trace_eval_myself'] ?? '',
      'parsed'  => $GLOBALS ['pad_trace_eval_parsed'] ?? '',
      'after'   => $GLOBALS ['pad_trace_eval_after']  ?? '',
      'result'  => $GLOBALS ['pad_trace_eval_result'] ?? ''
    ];
 
    pad_trace_write_error ( $txt, 'eval', $pad_eval_cnt, $data );

    $GLOBALS ['pad_trace_eval_stage'] = 'end';
    pad_timing_end ('eval');
    
    return '';

  }


  function pad_eval_trace ($step, $data) {

    if ( ! $GLOBALS['pad_trace_eval'] )
      return;

    global $pad_eval_cnt, $pad_eval_step_cnt, $pad_trace_dir_occ;

    $pad_eval_step_cnt++;

    pad_file_put_contents ( 
      "$pad_trace_dir_occ/eval/$pad_eval_cnt/$pad_eval_step_cnt.$step.json",  
      $data 
    );

  }
  

?>