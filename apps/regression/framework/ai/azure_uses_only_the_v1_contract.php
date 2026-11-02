<?php
  $padCheckSyntax = FALSE;
  $opts = [ 'provider' => 'azure', 'model' => 'my-deployment', 'key' => 'fixture-key', 'url' => 'https://fixture.openai.azure.com/openai/v1/chat/completions', 'ttl' => 0 ];
  padCurlFake ( [ $opts ['url'] => [ 'data' => [ 'choices' => [ [ 'message' => [ 'content' => 'Answer' ] ] ] ] ] ] );
  $answer = padAi ( 'Question', [ 'system' => 'Helpful', 'tokens' => 50 ] + $opts );
  $call = padCurlRecorded () [0];
  $body = json_decode ( $call ['post'], TRUE );
  $report = [ 'answer' => $answer, 'deployment' => $body ['model'], 'tokens' => $body ['max_completion_tokens'], 'system' => $body ['messages'] [0] ['content'],
    'key' => isset ( $call ['headers'] ['api-key'] ) ];
  foreach ( [ [ 'data' => [ 'choices' => [] ] ], [ 'status' => 429, 'data' => [ 'error' => [ 'message' => 'throttled' ] ] ] ] as $fake ) {
    padCurlFake ( [ $opts ['url'] => $fake ] ); $report ['errors'] [] = padAi ( 'Question', $opts ) === '' && padAiError () !== '';
  }
  padCurlFake ( [] );
  $report ['reject_versioned'] = padAi ( 'Question', [ 'url' => 'https://fixture.openai.azure.com/openai/deployments/x/chat/completions?api-version=2024-10-21' ] + $opts ) === '' && count ( padCurlRecorded () ) === 0;
  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
?>
