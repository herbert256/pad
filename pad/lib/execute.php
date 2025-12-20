<?php

  function padCode ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = FALSE;
    $GLOBALS ['padStrRes'] = FALSE;
    $GLOBALS ['padStrCln'] = FALSE;
    $GLOBALS ['padStrBld'] = 'code';

    return include PAD . 'start/enter/function.php';

  }

  function padSandbox ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = TRUE;
    $GLOBALS ['padStrRes'] = TRUE;
    $GLOBALS ['padStrCln'] = TRUE;
    $GLOBALS ['padStrBld'] = 'code';

    return include PAD . 'start/enter/function.php';

  }

  function padStrFun (  $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun ) {

    return include PAD . 'start/function.php';

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
