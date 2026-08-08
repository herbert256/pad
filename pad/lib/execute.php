<?php

  // Runs PAD source from inside PHP, and bridges between tags and pipe functions.
  //
  // padCode           renders a template string in the current variable scope and returns
  //                   the result; the general "evaluate this PAD fragment" call
  // padSandbox        the same, but sandboxed, reset and cleaned, so the fragment cannot
  //                   see or leave behind variables, and the four stores - data, content,
  //                   bool, sequence - are emptied going in and restored coming out
  //                   (used for _data/ files)
  // padStrFun         the full form, taking the sandbox/reset/clean flags plus a function
  //                   name; start/pad/parms.php calls it for {start function=...}
  // padFunctionAsTag  runs a pipe function as if it were a tag, by handing a synthetic
  //                   tag/value list to padEvalType; used by types/function.php
  // padTagAsFunction  the reverse - wraps value and parameters as {tag ...}value{/tag}
  //                   and renders it with padCode, so a tag can be used in a pipe

  function padCode ( $padStrCod ) {

    global $padStrBld, $padStrBox, $padStrCln, $padStrRes;

    $padStrBox = FALSE;
    $padStrRes = FALSE;
    $padStrCln = FALSE;
    $padStrBld = 'code';

    return include PAD . 'start/function.php';

  }

  function padSandbox ( $padStrCod ) {

    global $padStrBld, $padStrBox, $padStrCln, $padStrRes;

    $padStrBox = TRUE;
    $padStrRes = TRUE;
    $padStrCln = TRUE;
    $padStrBld = 'code';

    return include PAD . 'start/function.php';

  }

  function padStrFun (  $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun, $padStrBld = 'code' ) {

    return include PAD . 'start/pad/function.php';

  }

  function padFunctionAsTag ( $name, $myself, $parm ) {

    $k = 100;

    $result [$k] [0] = $name;
    $result [$k] [1] = 'TYPE';
    $result [$k] [2] = padTypeFunction ( $name );
    $result [$k] [3] = 0;

    foreach ( $parm as $key => $val )
      if ( $key > 0 ) {
        $k = $k + 100;
        $result [$k] [0] = $val;;
        $result [$k] [1] = 'VAL';
      }

    padEvalType ( $result, $myself );

    $start = array_key_first ( $result );

    return $result [$start] [0];

  }

  function padTagAsFunction ( $tag, $value, $parms ) {

    $extra = '';

    foreach ( $parms as $parm )
      $extra .= " '" . str_replace( "'", "\\'", $parm) . "',";

    if ( $extra )
      $extra = substr ( $extra, 0, -1 );

    return padCode ( '{' . $tag . $extra . '}' . $value . '{/' . $tag . '}' );

  }

?>