<?php

  // Cohere's Chat API v2 answers a message whose content is a list of blocks: the text blocks
  // are joined and a thinking block is passed over. An answer that finished with ERROR is an
  // error, its partial text never printed, and an error answer - here 429 Too Many Requests -
  // says itself in message. The key is read from CO_API_KEY, the name Cohere's SDKs read, when
  // COHERE_API_KEY is not set.

  $padCheckSyntax = FALSE;

  $_ENV ['CO_API_KEY'] = 'co-test';

  // The warnings its errors log go to a day's log no case reads, not to today's - the day a
  // case that reads the log may have frozen.

  padNowFreeze ( '2026-01-16 12:00:00' );

  padCurlFake ( [ 'https://api.cohere.com/v2/chat' => function ( $request ) {
    $prompt = json_decode ( $request ['post'], TRUE ) ['messages'] [0] ['content'] ?? '';
    return match ( $prompt ) {
      'blocks'  => [ 'data' => [ 'id' => 'c1', 'finish_reason' => 'COMPLETE',
                                 'message' => [ 'role' => 'assistant', 'content' => [ [ 'type' => 'thinking', 'thinking' => 'The capital...' ],
                                                                                      [ 'type' => 'text', 'text' => 'Par' ], [ 'type' => 'text', 'text' => 'is.' ] ] ] ] ],
      'broken'  => [ 'data' => [ 'id' => 'c2', 'finish_reason' => 'ERROR',
                                 'message' => [ 'role' => 'assistant', 'content' => [ [ 'type' => 'text', 'text' => 'Half an ans' ] ] ] ] ],
      'limited' => [ 'status' => 429, 'data' => [ 'id' => 'c3', 'message' => 'You are using a Trial key, which is limited to 10 API calls / minute.' ] ],
      default   => 500 };
  } ] );

  $asked = [];

  foreach ( [ 'blocks', 'broken', 'limited' ] as $prompt )
    $asked [$prompt] = padAi ( $prompt, [ 'provider' => 'cohere', 'ttl' => 0 ] ) ?: padAiError ();

  $asked ['bearer'] = padCurlRecorded () [0] ['headers'] ['Authorization'] ?? '';

  $json = json_encode ( $asked, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
