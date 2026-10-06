<?php

  // A {trace} tag loads the trace mode's library, and the error report asked whether that
  // library was there to decide whether a trace was running: after the tag had ended it
  // filed the report under the trace directory the tag had already put away - DATA/dumps/
  // <app>//ERROR, which the file layer refuses - and that refusal, a second error, replaced
  // the report of the first with the bare two messages. Asked as a browser asks: a tool's
  // user agent gets the JSON report, from another error action.

  $curl = padCurl ( [ 'url'     => $padHost . 'regression/pages/?request/a_trace_then_an_error&padInclude',
                      'options' => [ 'USERAGENT' => 'Mozilla/5.0' ] ] );

  $report = ( str_contains ( $curl ['data'], 'neverSetAfterTrace' ) and str_contains ( $curl ['data'], 'Level: ' )
              and ! str_contains ( $curl ['data'], 'Invalid file' ) ) ? 'yes' : 'NO';

?>
