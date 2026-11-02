<?php

$schema = [ 'type' => 'object', 'properties' => [ 'name' => [ 'type' => 'string', 'minLength' => 1 ], 'scores' => [ 'type' => 'array', 'items' => [ 'type' => 'integer', 'minimum' => 0 ], 'minItems' => 1 ] ], 'required' => [ 'name', 'scores' ], 'additionalProperties' => FALSE ];
padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'content' => '{"name":"Café","scores":[1,2]}' ] ] ] ] );
$text = padAi ( 'Give a JSON object', [ 'provider' => 'ollama', 'schema' => $schema ] );
$value = padAiStore ( 'json' );
$call = padCurlRecorded () [0];
$body = json_decode ( $call ['post'], TRUE );
$ok = $text === '{"name":"Café","scores":[1,2]}' && $value instanceof stdClass && $value->scores === [1,2]
   && ! isset ( $body ['schema'] ) && ! isset ( $body ['response_format'] ) && count ( padCurlRecorded () ) === 1;
$answer = $ok ? 'structured provider text is validated locally and decoded' : 'FAIL';

?>
