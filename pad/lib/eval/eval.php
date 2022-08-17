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

    $GLOBALS ['padEvalEval']    = $eval;
    $GLOBALS ['padEvalMySelf']  = $myself;
    $GLOBALS ['padEvalParsed']  = [];
    $GLOBALS ['padEvalAfter']   = [];
    $GLOBALS ['padEvalNow']     = [];

    $padEvalCnt++;
    $padEvalStepCnt = 0;
    $padEvalStart = $eval;
    $padEvalResult = [];

    padEvalTrace  ('start', [ 'eval' => $eval, 'myself' => $myself ] );

    $padEvalParse = padEvalParse ( $padEvalResult, $eval, $myself );

    if ( $padEvalParse )
      return padError ($padEvalParse);
    $GLOBALS ['padEvalParsed'] = $padEvalResult;

    padEvalTrace  ('parse', $padEvalResult );

    $padEvalAfter = padEvalAfter ( $padEvalResult, $eval );  
    if ( $padEvalAfter )
      return padError ($padEvalAfter);
    $GLOBALS ['padEvalAfter'] = $padEvalResult;

    padEvalTrace  ('after', $padEvalResult );

    padEvalGo ( $padEvalResult, array_key_first($padEvalResult), array_key_last($padEvalResult), $myself) ;

    $GLOBALS ['padTrace_go'] =  $padEvalResult;

    $key = array_key_first ($padEvalResult);
      
    if     ( count($padEvalResult) < 1        ) return padError("No result back");
    elseif ( count($padEvalResult) > 1        ) return padError("More then one result back");
    elseif ( isset($padEvalResult[$key][4])   ) return padError("Result is an array");
    elseif ( $padEvalResult[$key][1] <> 'VAL' ) return padError("Result is not a value");

    padEvalTrace ('end', $padEvalResult );
    padTimingEnd ('eval');
 
    return $padEvalResult [$key] [0];

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