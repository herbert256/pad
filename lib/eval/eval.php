<?php
  
  const pad_eval_precedence = [
    '**', '*', '/', '%', '+', '-',
    '.',
    'TYPE',
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

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $error_level = error_reporting(E_ALL);

    try {

      pad_eval_parse ( $pad_eval_result, $eval, $myself);

      if ( $pad_trace ) {
        $GLOBALS ['pad_trace_eval_parsed'] = $pad_eval_result;
        $GLOBALS ['pad_trace_eval_myself'] = $myself;
      }
      
      if ( ! pad_eval_after ( $pad_eval_result, $eval ) )
        return $pad_eval_start;
      
      pad_eval_go ( $pad_eval_result, array_key_first($pad_eval_result), array_key_last($pad_eval_result), $myself) ;

      $key = array_key_first ($pad_eval_result);
        
      if ( count($pad_eval_result) < 1 )
        $return = pad_eval_error("No result back");
      elseif ( count($pad_eval_result) > 1 )                                                
        $return = pad_eval_error("More then one result back");
      elseif ( isset($pad_eval_result[$key][6]) and $pad_eval_result [$key][6] == 'array' ) 
        $return = pad_eval_error("Result is an array");
      elseif ( isset($pad_eval_result[$key][1]) <> 'VAL' )         
        $return = pad_eval_error("Result is not a value");
      else
        $return = $pad_eval_result [$key] [0];
    }

    catch (Throwable $e) {

      return pad_eval_error ( $e->getMessage() . ' ' . $e->getFile() . '/' . $e->getLine() );
 
    }

    error_reporting($error_level);
    restore_error_handler();

    pad_trace ("eval/end", "nr=$pad_eval_cnt output=$return" );

    return $return;

  }

  function pad_eval_error ($txt) {

    global $pad_trace, $pad_eval_cnt, $pad_eval_start, $pad_eval_result, $app, $page, $PADREQID;

    $return = '';
    foreach ($pad_eval_result as $k => $one)
      if ( ($one[6]??'') == 'array' and isset($one[7]) )
        foreach ( $one [7] as $value)
          $return .= pad_info($value) . ' ';
      else
        $return .= $one[0] . ' ';   

    pad_trace ("eval/error", "nr=$pad_eval_cnt error=$txt result=$return");

    if ( $pad_trace ) {

      $json = pad_json ( [
        'eval'    => $pad_eval_start ?? '',
        'myself'  => $GLOBALS ['pad_trace_eval_myself'] ?? '',
        'error'   => $text ?? '',
        'nummer'  => $pad_eval_cnt ?? '',
        'parsed'  => $GLOBALS ['pad_trace_eval_parsed'] ?? '',
        'result'  => $pad_eval_result  ?? '',
        'app'     => $app  ?? '',
        'page'    => $page  ?? '',
        'request' => $PADREQID ?? ''        
      ] );

      $dir = $GLOBALS['pad_trace_dir_base'] . "/errors/eval/";
      if ( ! is_dir($dir) )
        mkdir ($dir, 0777, true);
      file_put_contents ("$dir/$pad_eval_cnt.json", $json );

      $dir = PAD_DATA . "errors/eval/$app/$page";
      if ( ! is_dir($dir) )
        mkdir ($dir, 0777, true);
      file_put_contents ( "$dir/$pad_eval_cnt.json", $json );

    }

    return $pad_eval_start;

  }

?>