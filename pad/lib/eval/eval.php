<?php
  
  const padEval_precedence = [
    '!',
    'TYPE',
    'FUN',
    '**', '*', '/', '%', '+', '-',
    '.',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'AND', 'XOR', 'OR',
    'NOT',
  ];

  const padEval_1   = [ '!', '+', '-', '*', '/', '%', '.' ];
  const padEval_2   = [ '**'];
  const padEval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];
  
  const padEval_alt = [ 
    '<'  => 'LT', 
    '<=' => 'LE', 
    '>'  => 'GT', 
    '>=' => 'GE', 
    '='  => 'EQ', 
    '==' => 'EQ', 
    '<>' => 'NE'
  ];

  function padEval ($eval, $myself='') {

    if ( strlen(trim($eval)) == 0 )
      return '';

    padTimingStart ('eval');

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

    padEvalTrace  ('start', [ 'eval' => $eval, 'myself' => $myself ] );

    $padEval_parse = padEvalParse ( $padEval_result, $eval, $myself );

    if ( $padEval_parse )
      return padEvalError ($padEval_parse);
    $GLOBALS ['padTrace_parsed'] = $padEval_result;

    padEvalTrace  ('parse', $padEval_result );

    $padEval_after = padEvalAfter ( $padEval_result, $eval );  
    if ( $padEval_after )
      return padEvalError ($padEval_after);
    $GLOBALS ['padTrace_after'] = $padEval_result;

    padEvalTrace  ('after', $padEval_result );

    padEvalGo ( $padEval_result, array_key_first($padEval_result), array_key_last($padEval_result), $myself) ;

    $GLOBALS ['padTrace_go'] =  $padEval_result;

    $key = array_key_first ($padEval_result);
      
    if     ( count($padEval_result) < 1        ) return padEvalError("No result back");
    elseif ( count($padEval_result) > 1        ) return padEvalError("More then one result back");
    elseif ( isset($padEval_result[$key][4])   ) return padEvalError("Result is an array");
    elseif ( $padEval_result[$key][1] <> 'VAL' ) return padEvalError("Result is not a value");

    padEvalTrace  ('end', $padEval_result );

    $GLOBALS ['padTrace_stage'] = 'end';

    padTimingEnd ('eval');
 
    return $padEval_result [$key] [0];

  }


  function padEvalError ($txt) {

    padEvalTrace  ('error', [ 'error' => $txt, 'result' => $GLOBALS ['padEval_result'] ] );

    $GLOBALS ['padTrace_stage'] = 'error';

    global $padEvalCnt;

    $data = [
      'eval'    => $GLOBALS ['padTrace_eval']   ?? '',
      'myself'  => $GLOBALS ['padTrace_myself'] ?? '',
      'parsed'  => $GLOBALS ['padTrace_parsed'] ?? '',
      'after'   => $GLOBALS ['padTrace_after']  ?? '',
      'result'  => $GLOBALS ['padTrace_result'] ?? ''
    ];
 
    padTraceWriteError ( $txt, 'eval', $padEvalCnt, $data );

    padTimingEnd ('eval');
    
    return padError ($txt);

    $GLOBALS ['padTrace_stage'] = 'end';

  }


  function padEvalTrace ($step, $data) {

    if ( ! $GLOBALS ['padTrace'] )
      return;

    global $pad, $padEvalCnt, $padEval_stepCnt, $padOccurDir;

    $padEval_stepCnt++;

    padFilePutContents ( 
      $padOccurDir [$pad] . "/eval/$padEvalCnt/$padEval_stepCnt.$step.json",  
      $data 
    );

  }
  

?>