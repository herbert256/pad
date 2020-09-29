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

    $return = '';
    foreach ($result as $k => $one)
      if ( $one[6]??'' == 'array' )
        foreach ( $one [7] as $value)
          $return .= $value . ' ';
      else
        $return .= $one[0] . ' ';

    pad_trace ("eval/end", "nr=$pad_eval_cnt output=" . $return);

    return trim($return);

  }
  
?>