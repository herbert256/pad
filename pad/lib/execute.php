<?php


  /**
   * Executes PAD template code and returns result.
   *
   * @param string $padStrCod PAD template code to execute.
   *
   * @return mixed Execution result.
   */
  function padCode ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = FALSE;
    $GLOBALS ['padStrRes'] = FALSE;
    $GLOBALS ['padStrCln'] = FALSE;
    $GLOBALS ['padStrBld'] = 'code';

    return include PAD . 'start/enter/function.php';

  }


  /**
   * Executes PAD code in sandbox mode with isolated scope.
   *
   * @param string $padStrCod PAD template code to execute.
   *
   * @return mixed Execution result.
   */
  function padSandbox ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = TRUE;
    $GLOBALS ['padStrRes'] = TRUE;
    $GLOBALS ['padStrCln'] = TRUE;
    $GLOBALS ['padStrBld'] = 'code';

    return include PAD . 'start/enter/function.php';

  }


  /**
   * Executes PAD code as a function with custom settings.
   *
   * @param string $padStrCod PAD template code.
   * @param bool   $padStrBox Sandbox mode flag.
   * @param bool   $padStrRes Reset flag.
   * @param bool   $padStrCln Clean flag.
   * @param string $padStrFun Function name.
   *
   * @return mixed Execution result.
   */
  function padStrFun (  $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun ) {

    return include PAD . 'start/function.php';

  }


  /**
   * Executes a function using the eval/type system.
   *
   * @param string $name   Function name.
   * @param string $myself Caller identifier.
   * @param array  $parm   Function parameters.
   *
   * @return mixed Function result.
   */
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


  /**
   * Executes a tag as if it were a function call.
   *
   * @param string $tag   Tag name.
   * @param string $value Content between tags.
   * @param array  $parms Tag parameters.
   *
   * @return mixed Tag result.
   */
  function padTagAsFunction ( $tag, $value, $parms ) {

    $extra = '';

    foreach ( $parms as $parm )
      $extra .= " '" . str_replace( "'", "\\'", $parm) . "',";

    if ( $extra )
      $extra = substr ( $extra, 0, -1 );

    return padCode ( '{' . $tag . $extra . '}' . $value . '{/' . $tag . '}' );

  }


?>
