<?php
  
  const pad_eval_precedence = [
    '**', '*', '/', '%', '+', '-',
    '.',
    'PHP',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'AND', 'XOR', 'OR',
    'NOT',
  ];

  const pad_eval_1   = [ '+', '-', '*', '/', '%', '.' ];
  const pad_eval_2   = [ '**'];
  const pad_eval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];

  function pad_eval ($eval, $myself='') {

    global $pad_trace, $pad_eval_cnt, $pad_eval_start, $pad_eval_result;

    $pad_eval_cnt++;
    $pad_eval_start = $eval;
    $pad_eval_result = [];

    pad_trace ("eval/start", "nr=$pad_eval_cnt input=$eval self=$myself");

    if ( strlen(trim($eval)) == 0 ) {
      pad_trace ("eval/empty", "nr=$pad_eval_cnt");
      return '';
    }

    pad_eval_parse ( $pad_eval_result, $eval, $myself);
    pad_eval_after ( $pad_eval_result );

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $error_level = error_reporting(E_ALL);
    try {
      pad_eval_go ( $pad_eval_result, array_key_first($pad_eval_result), array_key_last($pad_eval_result), $myself) ;
    }
    catch (Throwable $e) {
      return pad_eval_error ( $e->getMessage() );
    }
    error_reporting($error_level);
    restore_error_handler();

    $key = array_key_first ($pad_eval_result);
      
    if     ( count($pad_eval_result) < 1 )                                                return pad_eval_error("No result back");
    elseif ( count($pad_eval_result) > 1 )                                                return pad_eval_error("More then one result back");
    elseif ( isset($pad_eval_result[$key][6]) and $pad_eval_result [$key][6] == 'array' ) return pad_eval_error("Result is an array");
    elseif ( isset($pad_eval_result[$key][1]) <> 'VAL'                         )          return pad_eval_error("Result is not a value");
    else {
      pad_trace ("eval/end", "nr=$pad_eval_cnt output=" .$pad_eval_result [$key] [0] );
      return $pad_eval_result [$key] [0];
    }

  }

  function pad_eval_error ($txt) {

    global $pad_trace, $pad_eval_cnt, $pad_eval_start, $pad_eval_result;

    $return = '';
    foreach ($pad_eval_result as $k => $one)
      if ( ($one[6]??'') == 'array' )
        foreach ( $one [7] as $value)
          $return .= $value . ' ';
      else
        $return .= $one[0] . ' ';   

    pad_trace ("eval/error", "nr=$pad_eval_cnt error=$txt result=$return");

    pad_error ("Eval: $txt: $pad_eval_start --> $return");

    return '';

  }

?>