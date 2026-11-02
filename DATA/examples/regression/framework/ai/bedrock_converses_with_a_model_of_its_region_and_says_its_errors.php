<?php

  // Amazon Bedrock's Converse API at the endpoint of AWS_REGION (us-east-1 when it is not set),
  // the model percent-encoded in the address - an ARN too. The text blocks of the output message
  // are joined and a reasoning block is passed over; an answer a guardrail stopped is an error,
  // its text never printed, and a failure is said by the kind of x-amzn-ErrorType with its
  // message - a ThrottlingException is a 429. A region that is none is the author's mistake.

  $padCheckSyntax = FALSE;

  $_ENV ['AWS_BEARER_TOKEN_BEDROCK'] = 'bedrock-test';
  $_ENV ['AWS_REGION']               = 'us-west-2';

  // The warnings its errors log go to a day's log no case reads, not to today's - the day a
  // case that reads the log may have frozen.

  padNowFreeze ( '2026-01-16 12:00:00' );

  padCurlFake ( [ 'https://bedrock-runtime.*.amazonaws.com/model/*/converse' => function ( $request ) {
    $prompt = json_decode ( $request ['post'], TRUE ) ['messages'] [0] ['content'] [0] ['text'] ?? '';
    return match ( $prompt ) {
      'blocks'    => [ 'data' => [ 'output' => [ 'message' => [ 'role' => 'assistant', 'content' => [
                                     [ 'reasoningContent' => [ 'reasoningText' => [ 'text' => 'The capital...', 'signature' => 'c2ln' ] ] ],
                                     [ 'text' => 'Par' ], [ 'text' => 'is.' ] ] ] ],
                                   'stopReason' => 'end_turn', 'usage' => [ 'inputTokens' => 30, 'outputTokens' => 4, 'totalTokens' => 34 ], 'metrics' => [ 'latencyMs' => 312 ] ] ],
      'arn'       => [ 'data' => [ 'output' => [ 'message' => [ 'role' => 'assistant', 'content' => [ [ 'text' => 'Lyon.' ] ] ] ], 'stopReason' => 'end_turn' ] ],
      'guarded'   => [ 'data' => [ 'output' => [ 'message' => [ 'role' => 'assistant', 'content' => [ [ 'text' => 'Sorry, the model cannot answer this question.' ] ] ] ],
                                   'stopReason' => 'guardrail_intervened' ] ],
      'throttled' => [ 'status' => 429, 'headers' => [ 'x-amzn-ErrorType' => 'ThrottlingException:http://internal.amazon.com/coral/com.amazon.bedrock/' ],
                       'data' => [ 'message' => 'Too many requests, please wait before trying again.' ] ],
      default     => 500 };
  } ] );

  $asked = [];

  foreach ( [ 'blocks' => NULL, 'arn' => 'arn:aws:bedrock:us-west-2:123456789012:application-inference-profile/a1b2c3d4e5f6', 'guarded' => NULL, 'throttled' => NULL ] as $prompt => $model ) {
    $answer = padAi ( $prompt, [ 'provider' => 'bedrock', 'ttl' => 0 ] + ( $model ? [ 'model' => $model ] : [] ) );
    $calls  = padCurlRecorded ();
    $asked [$prompt] = [ 'url' => end ( $calls ) ['url'], 'answer' => $answer ?: padAiError () ];
  }

  $asked ['sent'] = json_decode ( padCurlRecorded () [0] ['post'], TRUE );
  $asked ['key']  = padCurlRecorded () [0] ['headers'] ['Authorization'] ?? '';

  unset ( $_ENV ['AWS_REGION'] );

  $answer = padAi ( 'arn', [ 'provider' => 'bedrock', 'ttl' => 0, 'system' => 'Answer in one word.' ] );
  $calls  = padCurlRecorded ();

  $asked ['no region'] = [ 'url' => end ( $calls ) ['url'], 'system' => json_decode ( end ( $calls ) ['post'], TRUE ) ['system'] ?? NULL, 'answer' => $answer ];

  $_ENV ['AWS_REGION'] = 'Frankfurt';

  $asked ['not a region'] = padAi ( 'arn', [ 'provider' => 'bedrock', 'ttl' => 0 ] ) ?: padAiError ();

  unset ( $_ENV ['AWS_REGION'] );

  $json = json_encode ( $asked, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
