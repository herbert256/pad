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
  // padFunctionAsTag  runs a pipe function as if it were a tag, through the evaluator's
  //                   own call (padEvalCall); used by types/function.php
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

  // Whether a name was hidden by an enclosing sandboxed pass - an application variable or a
  // store entry the pass started without. A miss of such a name is the isolation working,
  // and the strict check lets it through; a name that exists nowhere is reported as ever.
  // Asked for a field, a tag, and the rows named by lookup(), {tree}, {chart} and {pager}.
  // The head of a dotted, @ or prefixed name - $row.x, count@rows, data:rows - is what is
  // looked up.

  function padStrHidden ( $name ) {

    global $padStrCnt, $padStrStr, $padStrApp, $padStrStoDat;

    $name = (string) $name;

    if ( str_contains ( $name, ':' ) )
      $name = substr ( strrchr ( $name, ':' ), 1 );

    $name = preg_split ( '/[.@\[(\s]/', ltrim ( trim ( $name ), '$' ) ) [0] ?? '';

    for ( $i = (int) ( $padStrCnt ?? -1 ); $i >= 0; $i-- )
      if ( $padStrStr [$i] [1] ?? FALSE ) {
        if ( array_key_exists ( $name, $padStrApp [$i] ?? [] ) )
          return TRUE;
        foreach ( $padStrStoDat [$i] ?? [] as $padStrOne )
          if ( is_array ( $padStrOne ) and array_key_exists ( $name, $padStrOne ) )
            return TRUE;
      }

    return FALSE;

  }

  function padStrFun (  $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun, $padStrBld = 'code' ) {

    return include PAD . 'start/pad/function.php';

  }

  function padFunctionAsTag ( $name, $myself, $parm ) {

    $kind = padTypeFunction ( $name );

    if ( $kind and file_exists ( PAD . "eval/single/$kind.php" ) )
      return padEvalLook ( $kind, $name );

    // [0] holds the tag's parameters as written; the ones after it are the function's.

    $args = [];

    foreach ( $parm as $key => $val )
      if ( $key > 0 )
        $args [] = $val;

    return padEvalCall ( $kind, $name, $myself, $args );

  }

  function padTagAsFunction ( $tag, $value, $parms ) {

    $extra = '';

    foreach ( $parms as $parm )
      $extra .= " '" . str_replace ( [ '\\', "'" ], [ '\\\\', "\\'" ], (string) $parm ) . "',";

    if ( $extra )
      $extra = substr ( $extra, 0, -1 );

    return padCode ( '{' . $tag . $extra . '}' . $value . '{/' . $tag . '}' );

  }

?>
