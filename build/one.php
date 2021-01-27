<?php

  pad_trace ("app/one", "one=$pad_one" );

  $pad_one_return = '';

  if ( $pad_mode == 'isolate' )
    $pad_one_return = '{isolate}';    

  if ( $pad_mode == 'demand' $pad_mode == 'isolate' )

    $pad_one_return .= "{call '$pad_one.php'}";

  else {

    $pad_call = "$pad_one.php";
    $pad_one_return .= include PAD_HOME . 'level/call.php';

  }

  if ( ! ( isset($_REQUEST['pad_include']) and ( substr($pad_one -6) == '/inits' or substr($pad_one -6) == '/exits') ) )
      $pad_one_return .= pad_get_html ( "$pad_one.html" );

  if ( $pad_mode == 'isolate' )
    $pad_one_return = '{/isolate}';

  return $pad_one_return;

?>