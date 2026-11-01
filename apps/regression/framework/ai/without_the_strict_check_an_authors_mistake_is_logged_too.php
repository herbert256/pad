<?php

  // Without the strict check a missing key, an unknown provider or a wrong option of padAi
  // and {ai} answers nothing, padAiError says why - and the application's log has it, as it
  // has an error of the API: the page goes on, and the log is where the mistake shows.

  $padCheckSyntax = FALSE;

  padCurlFake ( [ '*' => 500 ] );

  // A day's log of its own, which no other case writes to while this one reads it.

  padNowFreeze ( '2026-01-14 12:00:00' );

  $log    = DATA . 'logs/regression/framework/2026-01-14.log';
  $before = is_file ( $log ) ? count ( file ( $log ) ) : 0;

  $answers = [ padAi ( 'hello', [ 'provider' => 'anthropic', 'key' => '', 'ttl' => 0 ] ),
               padAi ( 'hello', [ 'provider' => 'nosuch', 'ttl' => 0 ] ),
               padAi ( 'hello', [ 'provider' => 'openai', 'key' => 'k', 'tokens' => 0 ] ) ];

  $lines = is_file ( $log ) ? preg_grep ( '/needs an API key|no AI provider named|tokens is the most/', array_slice ( file ( $log ), $before ) ) : [];

  $json = json_encode ( [ 'answers' => $answers,
                          'logged'  => array_values ( array_map ( fn ( $line ) => preg_replace ( '/^\S+ \S+ (\S+) (.*?) \{.*$/s', '$1 $2', trim ( $line ) ), $lines ) ),
                          'fetched' => count ( padCurlRecorded () ) ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
