<?php

  // Workers AI runs a model under the account of CLOUDFLARE_ACCOUNT_ID, its name a part of the
  // address. Most of its text generation models answer a response; some - Qwen3 - answer in
  // the form of a chat completion, its first choice's message read then. A failure says itself
  // in errors, a message and a code: 3036, the day's free allocation used up, is a 429. With
  // url= - an AI Gateway's address - the account is not needed. The tag takes a model of the
  // catalogue as written, its @ no placeholder.

  $padCheckSyntax = FALSE;

  $_ENV ['CLOUDFLARE_ACCOUNT_ID'] = '023e105f4ecef8ad9ca31a8372d0c353';
  $_ENV ['CLOUDFLARE_API_TOKEN']  = 'cf-test';

  // The warnings its errors log go to a day's log no case reads, not to today's - the day a
  // case that reads the log may have frozen.

  padNowFreeze ( '2026-01-16 12:00:00' );

  padCurlFake ( [
    'https://api.cloudflare.com/client/v4/accounts/023e105f4ecef8ad9ca31a8372d0c353/ai/run/*' => function ( $request ) {
      $messages = json_decode ( $request ['post'], TRUE ) ['messages'] ?? [];
      $prompt   = end ( $messages ) ['content'] ?? '';
      return match ( $prompt ) {
        'response' => [ 'data' => [ 'result' => [ 'response' => 'Paris.', 'usage' => [ 'prompt_tokens' => 20, 'completion_tokens' => 3, 'total_tokens' => 23 ] ],
                                    'success' => TRUE, 'errors' => [], 'messages' => [] ] ],
        'choices'  => [ 'data' => [ 'result' => [ 'id' => 'chatcmpl-1', 'object' => 'chat.completion', 'model' => '@cf/qwen/qwen3-30b-a3b-fp8',
                                                  'choices' => [ [ 'index' => 0, 'message' => [ 'role' => 'assistant', 'content' => 'Lyon.' ], 'finish_reason' => 'stop' ] ] ],
                                    'success' => TRUE, 'errors' => [], 'messages' => [] ] ],
        'limited'  => [ 'status' => 429, 'data' => [ 'result' => NULL, 'success' => FALSE, 'messages' => [],
                                                     'errors' => [ [ 'code' => 3036, 'message' => 'Account limited: you have used up your daily free allocation of 10,000 neurons.' ] ] ] ],
        default    => 500 };
    },
    'https://gateway.ai.cloudflare.com/*' => [ 'data' => [ 'result' => [ 'response' => 'Marseille.' ], 'success' => TRUE, 'errors' => [], 'messages' => [] ] ],
  ] );

  $asked = [];

  foreach ( [ 'response' => '@cf/meta/llama-3.3-70b-instruct-fp8-fast', 'choices' => '@cf/qwen/qwen3-30b-a3b-fp8', 'limited' => '@cf/meta/llama-3.3-70b-instruct-fp8-fast' ] as $prompt => $model ) {
    $answer = padAi ( $prompt, [ 'provider' => 'cloudflare', 'model' => $model, 'system' => 'Answer in one word.', 'ttl' => 0 ] );
    $calls  = padCurlRecorded ();
    $asked [$prompt] = [ 'url' => end ( $calls ) ['url'], 'answer' => $answer ?: padAiError () ];
  }

  $asked ['sent'] = json_decode ( padCurlRecorded () [0] ['post'], TRUE );

  unset ( $_ENV ['CLOUDFLARE_ACCOUNT_ID'] );

  $asked ['gateway'] = padAi ( 'Name a port.', [ 'provider' => 'cloudflare', 'ttl' => 0,
                                                 'url' => 'https://gateway.ai.cloudflare.com/v1/023e105f4ecef8ad9ca31a8372d0c353/pages/workers-ai/@cf/meta/llama-3.3-70b-instruct-fp8-fast' ] )
                       ?: padAiError ();

  $asked ['no account'] = padAi ( 'Name a port.', [ 'provider' => 'cloudflare', 'ttl' => 0 ] ) ?: padAiError ();

  $_ENV ['CLOUDFLARE_ACCOUNT_ID'] = '023e105f4ecef8ad9ca31a8372d0c353';

  $json = json_encode ( $asked, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
