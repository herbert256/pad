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

    global $pad_trace, $pad_eval_cnt;

    $pad_eval_cnt++;

    if ( $pad_trace )
      pad_trace ("eval/start", "nr=$pad_eval_cnt input=" . "$eval/$myself");

    $tmp = trim($eval);
    if ( strlen($tmp) == 0 ) {
      pad_trace ("eval/end", "nr=$pad_eval_cnt output=");
      return '';
    }

    pad_eval_parse ( $result, $eval, $myself);
    pad_eval_after ( $result );
    pad_eval_go    ( $result, array_key_first($result), array_key_last($result), $myself);

    $return = [];

    foreach ($result as $k => $one)
      if ( $one[6]??'' == 'array' )
        foreach ( $one [7] as $value)
          $return [] = (string) $value;
      else
        $return [] = (string) $one[0];

    if ( $pad_trace ) {
      if ( count($return) == 0 )
        $eval_trace = '*EMPTY*';
      elseif ( count($return) == 1 )
        $eval_trace = $return [0];
      else
        $eval_trace = 'ARRAY: ' . implode('|', $return);
      pad_trace ("eval/end", "nr=$pad_eval_cnt output=" . $eval_trace);
    }

    if ( count($return) == 0 )
      $eval_return = '';
    elseif ( count($return) == 1 )
      $eval_return = $return [0];
    else
      $eval_return = implode (' ', $return);

    return $eval_return;

  }
  
?>