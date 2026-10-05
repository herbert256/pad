<?php

  // padRescue answers the callback's value, or on a Throwable - an Exception or an Error -
  // the rescue value, a Closure given the Throwable. With $report the failure goes to PHP's
  // error log: pointed at a file of our own here, and read back.

  $boom = function () { throw new RuntimeException ( 'boom' ); };

  $log = tempnam ( sys_get_temp_dir (), 'padRescue' );
  $was = ini_set ( 'error_log', $log );

  $r = json_encode ( [
    padRescue ( fn () => 'fine' ),
    padRescue ( $boom, 'fallback', FALSE ),
    padRescue ( $boom, fn ( $e ) => 'rescued: ' . $e->getMessage (), FALSE ),
    padRescue ( fn () => intdiv ( 1, 0 ), 'an Error too', FALSE ),
    padRescue ( $boom ),
    padRescue ( fn () => NULL, 'not used' )
  ] );

  ini_set ( 'error_log', $was );

  $logged = preg_match ( '/padRescue: RuntimeException: boom in .*values_rescue_answers_the_fallback_when_the_callback_throws\.php:\d+/',
                         (string) file_get_contents ( $log ) ) ? 'reported' : 'not reported';

  $lines = count ( file ( $log ) );

  unlink ( $log );

?>
