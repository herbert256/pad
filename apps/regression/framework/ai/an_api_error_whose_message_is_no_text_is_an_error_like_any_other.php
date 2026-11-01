<?php

  // url= points a provider at any server that speaks its API, and their error answers take
  // many shapes: a message that is a list or an object - a validation error's location and
  // text - is an error like any other, said as its JSON, where it ended the page with
  // "Array to string conversion". With the strict check off the tag answers nothing.

  $padCheckSyntax = FALSE;

  $_ENV ['OPENAI_API_KEY'] = 'sk-test';

  padCurlFake ( [ '*' => [ 'status' => 422, 'data' => [ 'error' => [ 'message' => [ 'loc' => 'body', 'msg' => 'field required' ] ] ] ] ] );

  // The warning it logs goes to a day's log of its own: the cases that read the log, run at
  // the same time, find only their own lines in theirs.

  padNowFreeze ( '2026-01-13 12:00:00' );

?>
