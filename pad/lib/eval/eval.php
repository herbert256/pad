<?php
  
  const pEval_precedence = [
    '!',
    'TYPE',
    'FUN',
    '**', '*', '/', '%', '+', '-',
    '.',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'AND', 'XOR', 'OR',
    'NOT',
  ];

  const pEval_1   = [ '!', '+', '-', '*', '/', '%', '.' ];
  const pEval_2   = [ '**'];
  const pEval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];
  
  const pEval_alt = [ 
    '<'  => 'LT', 
    '<=' => 'LE', 
    '>'  => 'GT', 
    '>=' => 'GE', 
    '='  => 'EQ', 
    '==' => 'EQ', 
    '<>' => 'NE'
  ];

  function pEval ($eval, $myself='') {

    if ( strlen(trim($eval)) == 0 )
      return '';

    pTiming_start ('eval');

    global $pEvalCnt, $pEval_stepCnt, $pEval_start, $pEval_result, $pTrace;

    $GLOBALS ['pTrace_stage']   = 'start';
    $GLOBALS ['pTrace_eval']    = $eval;
    $GLOBALS ['pTrace_myself']  = $myself;
    $GLOBALS ['pTrace_parsed']  = [];
    $GLOBALS ['pTrace_after']   = [];
    $GLOBALS ['pTrace_now']     = [];

    $pEvalCnt++;
    $pEval_stepCnt = 0;
    $pEval_start = $eval;
    $pEval_result = [];

    pEval_trace  ('start', [ 'eval' => $eval, 'myself' => $myself ] );

    $pEval_parse = pEval_parse ( $pEval_result, $eval, $myself );

    if ( $pEval_parse )
      return pEval_error ($pEval_parse);
    $GLOBALS ['pTrace_parsed'] = $pEval_result;

    pEval_trace  ('parse', $pEval_result );

    $pEval_after = pEval_after ( $pEval_result, $eval );  
    if ( $pEval_after )
      return pEval_error ($pEval_after);
    $GLOBALS ['pTrace_after'] = $pEval_result;

    pEval_trace  ('after', $pEval_result );

    pEval_go ( $pEval_result, array_key_first($pEval_result), array_key_last($pEval_result), $myself) ;

    $GLOBALS ['pTrace_go'] =  $pEval_result;

    $key = array_key_first ($pEval_result);
      
    if ( count($pEval_result) < 1 )
      return pEval_error("No result back");
    elseif ( count($pEval_result) > 1 )                                                
      return pEval_error("More then one result back");
    elseif ( isset($pEval_result[$key][4]) ) 
      return pEval_error("Result is an array");
    elseif ( $pEval_result[$key][1] <> 'VAL' )         
      return pEval_error("Result is not a value");

    pEval_trace  ('end', $pEval_result );

    $GLOBALS ['pTrace_stage'] = 'end';

    pTiming_end ('eval');
 
    return $pEval_result [$key] [0];

  }


  function pEval_error ($txt) {

    pEval_trace  ('error', [ 'error' => $txt, 'result' => $GLOBALS ['pEval_result'] ] );

    $GLOBALS ['pTrace_stage'] = 'error';

    global $pEvalCnt;

    $data = [
      'eval'    => $GLOBALS ['pTrace_eval']   ?? '',
      'myself'  => $GLOBALS ['pTrace_myself'] ?? '',
      'parsed'  => $GLOBALS ['pTrace_parsed'] ?? '',
      'after'   => $GLOBALS ['pTrace_after']  ?? '',
      'result'  => $GLOBALS ['pTrace_result'] ?? ''
    ];
 
    pTrace_write_error ( $txt, 'eval', $pEvalCnt, $data );

    $GLOBALS ['pTrace_stage'] = 'end';
    pTiming_end ('eval');
    
    return '';

  }


  function pEval_trace ($step, $data) {

    if ( ! $GLOBALS['pTrace'] )
      return;

    global $p, $pEvalCnt, $pEval_stepCnt, $pOccurDir;

    $pEval_stepCnt++;

    pFile_put_contents ( 
      $pOccurDir [$p] . "/eval/$pEvalCnt/$pEval_stepCnt.$step.json",  
      $data 
    );

  }
  

?>