<?php

  // bedrock - Amazon Bedrock, its Converse API (docs.aws.amazon.com/bedrock/latest/APIReference/
  // API_runtime_Converse.html): POST https://bedrock-runtime.<region>.amazonaws.com/model/<model>/converse,
  // one interface for every model of Bedrock that takes messages. The key is a Bedrock API key in
  // AWS_BEARER_TOKEN_BEDROCK - the name the AWS SDKs read - sent as a bearer token; a request
  // signed with an access key (SigV4) is not made here. The region is AWS_REGION, else
  // AWS_DEFAULT_REGION, else us-east-1; the model us.anthropic.claude-sonnet-4-6 - an inference
  // profile of the us regions - unless model= names another: a model id, an inference profile or
  // an ARN, percent-encoded in the address as the SDKs do. The system prompt is a system block,
  // the prompt the user's message of one text block, inferenceConfig.maxTokens the limit. The
  // answer is the output message: its text blocks joined, a reasoning block passed over. An
  // answer a guardrail stopped or that was filtered (stopReason guardrail_intervened,
  // content_filtered) is an error, its text never printed. A failure is said by its kind - the
  // x-amzn-ErrorType header: ValidationException 400, AccessDeniedException 403,
  // ResourceNotFoundException 404, ModelTimeoutException 408, ModelErrorException 424,
  // ThrottlingException and ModelNotReadyException 429, 500, 503 - and its message.

  $region = function () {
    foreach ( [ 'AWS_REGION', 'AWS_DEFAULT_REGION' ] as $name )
      if ( is_string ( $value = padEnv ( $name, '' ) ) and trim ( $value ) !== '' )
        return trim ( $value );
    return 'us-east-1';
  };

  return [
    'name'    => 'Amazon Bedrock',
    'key'     => 'AWS_BEARER_TOKEN_BEDROCK',
    'model'   => 'us.anthropic.claude-sonnet-4-6',
    'tokens'  => 4096,
    'check'   => fn ( $ask, $url ) => ( $url !== NULL or preg_match ( '/^[a-z]{2}(-[a-z]+)+-\d{1,2}$/D', $region () ) ) ? ''
                                      : 'the region of Amazon Bedrock is a name like us-east-1 - not ' . padMakeSafe ( $region (), 40 ),
    'url'     => fn ( $ask ) => 'https://bedrock-runtime.' . $region () . '.amazonaws.com/model/' . rawurlencode ( $ask ['model'] ) . '/converse',
    'headers' => fn ( $ask ) => [ 'Authorization' => 'Bearer ' . $ask ['key'] ],
    'body'    => fn ( $ask ) => [ 'messages' => [ [ 'role' => 'user', 'content' => [ [ 'text' => $ask ['prompt'] ] ] ] ] ]
                                + ( $ask ['system'] !== '' ? [ 'system' => [ [ 'text' => $ask ['system'] ] ] ] : [] )
                                + [ 'inferenceConfig' => [ 'maxTokens' => $ask ['tokens'] ] ],
    'text'    => function ( $answer ) {
      if ( in_array ( $answer ['stopReason'] ?? '', [ 'guardrail_intervened', 'content_filtered' ], TRUE ) )
        return '';
      $text = '';
      foreach ( (array) ( $answer ['output'] ['message'] ['content'] ?? [] ) as $block )
        if ( is_string ( $block ['text'] ?? NULL ) )
          $text .= $block ['text'];
      return $text;
    },
    'error'   => function ( $answer, $status, $headers = [] ) {
      $stop = $answer ['stopReason'] ?? '';
      if ( $stop === 'guardrail_intervened' )
        return 'a guardrail stopped the answer (guardrail_intervened)';
      if ( $stop === 'content_filtered' )
        return 'the answer was filtered (content_filtered)';
      $kind = '';
      foreach ( (array) $headers as $name => $value )
        if ( strtolower ( (string) $name ) === 'x-amzn-errortype' )
          $kind = (string) $value;
      if ( $kind === '' and is_string ( $answer ['__type'] ?? NULL ) )
        $kind = $answer ['__type'];
      $kind = preg_replace ( '/^.*#|:.*$/s', '', $kind );
      $said = $answer ['message'] ?? $answer ['Message'] ?? '';
      $said = is_scalar ( $said ) ? trim ( (string) $said ) : '';
      return $kind === '' ? $said : ( $said === '' ? $kind : "$kind: $said" );
    },
  ];

?>
