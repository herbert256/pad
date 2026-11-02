<?php

$padCheckSyntax = FALSE;
$ok = TRUE;
foreach ( [ 'plain text', '```json\n{}\n```', '{"n":"2"}', '{"n":2,"extra":true}', '[]' ] as $text ) {
  padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'content' => $text ] ] ] ] );
  $answer = padAi ( 'JSON please', [ 'provider' => 'ollama', 'schema' => [ 'type' => 'object', 'properties' => [ 'n' => [ 'type' => 'integer' ] ], 'required' => [ 'n' ], 'additionalProperties' => FALSE ] ] );
  $ok = $ok && $answer === '' && str_contains ( padAiError (), 'invalid structured data' ) && padAiStore ( 'json' ) === NULL;
}
$answer = $ok ? 'invalid structured data is rejected without leaking into answers' : 'FAIL';

?>
