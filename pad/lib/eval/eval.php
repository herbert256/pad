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

    global $padEvalCnt, $padEvalStepCnt, $padEvalStart, $padEvalResult, $padTrace;

    $GLOBALS ['padTrace_stage']   = 'start';
    $GLOBALS ['padTrace_eval']    = $eval;
    $GLOBALS ['padTrace_myself']  = $myself;
    $GLOBALS ['padTrace_parsed']  = [];
    $GLOBALS ['padTrace_after']   = [];
    $GLOBALS ['padTrace_now']     = [];

    $padEvalCnt++;
    $padEvalStepCnt = 0;
    $padEvalStart = $eval;
    $padEvalResult = [];

    padEvalTrace  ('start', [ 'eval' => $eval, 'myself' => $myself ] );

    $padEvalParse = padEvalParse ( $padEvalResult, $eval, $myself );

    if ( $padEvalParse )
      return padEvalError ($padEvalParse);
    $GLOBALS ['padTrace_parsed'] = $padEvalResult;

    padEvalTrace  ('parse', $padEvalResult );

    $padEvalAfter = padEvalAfter ( $padEvalResult, $eval );  
    if ( $padEvalAfter )
      return padEvalError ($padEvalAfter);
    $GLOBALS ['padTrace_after'] = $padEvalResult;

    padEvalTrace  ('after', $padEvalResult );

    padEvalGo ( $padEvalResult, array_key_first($padEvalResult), array_key_last($padEvalResult), $myself) ;

    $GLOBALS ['padTrace_go'] =  $padEvalResult;

    $key = array_key_first ($padEvalResult);
      
    if     ( count($padEvalResult) < 1        ) return padEvalError("No result back");
    elseif ( count($padEvalResult) > 1        ) return padEvalError("More then one result back");
    elseif ( isset($padEvalResult[$key][4])   ) return padEvalError("Result is an array");
    elseif ( $padEvalResult[$key][1] <> 'VAL' ) return padEvalError("Result is not a value");

    padEvalTrace  ('end', $padEvalResult );

    $GLOBALS ['padTrace_stage'] = 'end';

    padTimingEnd ('eval');
 
    return $padEvalResult [$key] [0];

  }


  function padEvalError ($txt) {

    padEvalTrace  ('error', [ 'error' => $txt, 'result' => $GLOBALS ['padEvalResult'] ] );

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

    global $pad, $padEvalCnt, $padEvalStepCnt, $padOccurDir;

    $padEvalStepCnt++;

    padFilePutContents ( 
      $padOccurDir [$pad] . "/eval/$padEvalCnt/$padEvalStepCnt.$step.json",  
      $data 
    );

  }
  

?>