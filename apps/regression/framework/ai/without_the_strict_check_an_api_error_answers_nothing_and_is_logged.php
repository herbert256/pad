<?php

  // With the strict check off an error of the API is no error of the page: the tag answers
  // nothing, padAiError says what the provider said, and a warning goes to the application's
  // log. A refusal that stays one - Anthropic's stop_reason refusal - is such an error too: its
  // text is never printed as the answer.

  $padCheckSyntax = FALSE;

  $_ENV ['ANTHROPIC_API_KEY'] = 'sk-test';

  padCurlFake ( [
    'https://api.openai.com/*'    => [ 'status' => 401, 'data' => [ 'error' => [ 'message' => 'Incorrect API key provided: sk-bad.', 'type' => 'invalid_request_error' ] ] ],
    'https://api.anthropic.com/*' => [ 'data' => [ 'stop_reason' => 'refusal', 'stop_details' => [ 'type' => 'refusal', 'category' => 'cyber' ], 'content' => [] ] ],
  ] );

  padNowFreeze ( '2026-11-01 12:00:00' );

  $log = DATA . 'logs/regression/framework/2026-11-01.log';

  $before = is_file ( $log ) ? count ( file ( $log ) ) : 0;

  $direct = padAi ( 'Hello?', [ 'provider' => 'openai', 'key' => 'sk-bad' ] );
  $error  = padAiError ();

  function aiLenientLogged () {
    global $log, $before;
    $lines = is_file ( $log ) ? preg_grep ( '/did not answer/', array_slice ( file ( $log ), $before ) ) : [];
    return implode ( ' / ', array_map ( fn ( $line ) => preg_replace ( '/^\S+ \S+ (\S+) (.*?) \{.*$/s', '$1 $2', trim ( $line ) ), $lines ) );
  }

?>
