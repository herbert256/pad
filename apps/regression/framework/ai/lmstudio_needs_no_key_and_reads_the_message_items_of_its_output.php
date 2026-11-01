<?php

  // LM Studio's native chat API on this machine: no key, unless LM_API_TOKEN is set - then it
  // goes as a bearer token. The prompt is the input, the system prompt the system_prompt; the
  // answer is the content of the message items of the output, a reasoning item passed over. An
  // error says its message - also one answered with a 200, as LM Studio answers a wrong path.

  $padCheckSyntax = FALSE;

  // The warnings its errors log go to a day's log no case reads, not to today's - the day a
  // case that reads the log may have frozen.

  padNowFreeze ( '2026-01-16 12:00:00' );

  padCurlFake ( [
    'http://127.0.0.1:1234/api/v1/chat' => function ( $request ) {
      return match ( json_decode ( $request ['post'], TRUE ) ['model'] ?? '' ) {
        'ibm/granite-4-micro' => [ 'data' => [ 'model_instance_id' => 'ibm/granite-4-micro',
                                               'output' => [ [ 'type' => 'reasoning', 'content' => 'Three rivers...' ],
                                                             [ 'type' => 'message', 'content' => 'Loire, Seine, Rhone.' ] ],
                                               'stats' => [ 'input_tokens' => 21, 'total_output_tokens' => 9, 'reasoning_output_tokens' => 3, 'tokens_per_second' => 48.2, 'time_to_first_token_seconds' => 0.2 ] ] ],
        'nosuch/model'        => [ 'status' => 404, 'data' => [ 'error' => [ 'message' => 'Model not found: nosuch/model', 'type' => 'invalid_request_error',
                                                                               'param' => 'model', 'code' => 'model_not_found' ] ] ],
        default               => 500 };
    },
    'http://gpu.lan:1234/api/v1/chats' => [ 'data' => [ 'error' => 'Unexpected endpoint or method. (POST /api/v1/chats)' ] ],
  ] );

  $asked = [];

  $asked ['answer'] = padAi ( 'Name three rivers of France.', [ 'provider' => 'lmstudio', 'system' => 'Answer in one line.', 'tokens' => 40, 'ttl' => 0 ] );
  $asked ['sent']   = json_decode ( padCurlRecorded () [0] ['post'], TRUE );
  $asked ['token']  = padCurlRecorded () [0] ['headers'] ['Authorization'] ?? '(none)';

  $_ENV ['LM_API_TOKEN'] = 'lm-token';

  padAi ( 'Name three rivers of France.', [ 'provider' => 'lmstudio', 'ttl' => 0 ] );

  $asked ['with LM_API_TOKEN'] = padCurlRecorded () [1] ['headers'] ['Authorization'] ?? '(none)';

  unset ( $_ENV ['LM_API_TOKEN'] );

  $asked ['no model']   = padAi ( 'Hello?', [ 'provider' => 'lmstudio', 'model' => 'nosuch/model', 'ttl' => 0 ] ) ?: padAiError ();
  $asked ['wrong path'] = padAi ( 'Hello?', [ 'provider' => 'lmstudio', 'url' => 'http://gpu.lan:1234/api/v1/chats', 'ttl' => 0 ] ) ?: padAiError ();

  $json = json_encode ( $asked, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
