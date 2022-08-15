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

    global $padEvalCnt, $padEval_stepCnt, $padEval_start, $padEval_result, $padTrace;

    $GLOBALS ['padTrace_stage']   = 'start';
    $GLOBALS ['padTrace_eval']    = $eval;
    $GLOBALS ['padTrace_myself']  = $myself;
    $GLOBALS ['padTrace_parsed']  = [];
    $GLOBALS ['padTrace_after']   = [];
    $GLOBALS ['padTrace_now']     = [];

    $padEvalCnt++;
    $padEval_stepCnt = 0;
    $padEval_start = $eval;
    $padEval_result = [];

    pEval_trace  ('start', [ 'eval' => $eval, 'myself' => $myself ] );

    $padEval_parse = pEval_parse ( $padEval_result, $eval, $myself );

    if ( $padEval_parse )
      return pEval_error ($padEval_parse);
    $GLOBALS ['padTrace_parsed'] = $padEval_result;

    pEval_trace  ('parse', $padEval_result );

    $padEval_after = pEval_after ( $padEval_result, $eval );  
    if ( $padEval_after )
      return pEval_error ($padEval_after);
    $GLOBALS ['padTrace_after'] = $padEval_result;

    pEval_trace  ('after', $padEval_result );

    pEval_go ( $padEval_result, array_key_first($padEval_result), array_key_last($padEval_result), $myself) ;

    $GLOBALS ['padTrace_go'] =  $padEval_result;

    $key = array_key_first ($padEval_result);
      
    if     ( count($padEval_result) < 1        ) return pEval_error("No result back");
    elseif ( count($padEval_result) > 1        ) return pEval_error("More then one result back");
    elseif ( isset($padEval_result[$key][4])   ) return pEval_error("Result is an array");
    elseif ( $padEval_result[$key][1] <> 'VAL' ) return pEval_error("Result is not a value");

    pEval_trace  ('end', $padEval_result );

    $GLOBALS ['padTrace_stage'] = 'end';

    pTiming_end ('eval');
 
    return $padEval_result [$key] [0];

  }


  function pEval_error ($txt) {

    pEval_trace  ('error', [ 'error' => $txt, 'result' => $GLOBALS ['padEval_result'] ] );

    $GLOBALS ['padTrace_stage'] = 'error';

    global $padEvalCnt;

    $data = [
      'eval'    => $GLOBALS ['padTrace_eval']   ?? '',
      'myself'  => $GLOBALS ['padTrace_myself'] ?? '',
      'parsed'  => $GLOBALS ['padTrace_parsed'] ?? '',
      'after'   => $GLOBALS ['padTrace_after']  ?? '',
      'result'  => $GLOBALS ['padTrace_result'] ?? ''
    ];
 
    pTrace_write_error ( $txt, 'eval', $padEvalCnt, $data );

    pTiming_end ('eval');
    
    return pError ($txt);

    $GLOBALS ['padTrace_stage'] = 'end';

  }


  function pEval_trace ($step, $data) {

    if ( ! $GLOBALS ['padTrace'] )
      return;

    global $pad, $padEvalCnt, $padEval_stepCnt, $padOccurDir;

    $padEval_stepCnt++;

    pFile_put_contents ( 
      $padOccurDir [$pad] . "/eval/$padEvalCnt/$padEval_stepCnt.$step.json",  
      $data 
    );

  }
  

?>