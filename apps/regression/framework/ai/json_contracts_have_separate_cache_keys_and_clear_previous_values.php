<?php

include_once PAD . 'subs/ai/_lib/ai.php';
$ask = [ 'provider' => 'ollama', 'model' => 'llama3.2', 'system' => '', 'prompt' => 'A number', 'tokens' => 20 ];
$plain = padAiCacheKey ( $ask, 'http://localhost/api/chat' );
$json = padAiCacheKey ( $ask + [ 'json' => TRUE ], 'http://localhost/api/chat' );
$schema = padAiCacheKey ( $ask + [ 'json' => TRUE, 'schema' => [ 'type' => 'integer' ] ], 'http://localhost/api/chat' );
padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'content' => 'null' ] ] ] ] );
$valid = padAi ( 'JSON null please', [ 'provider' => 'ollama', 'json' => TRUE ] );
$ok = count ( array_unique ( [ $plain, $json, $schema ] ) ) === 3 && $valid === 'null' && padAiError () === '';
padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'content' => '{"x":1}' ] ] ] ] );
padAi ( 'JSON object please', [ 'provider' => 'ollama', 'json' => TRUE ] );
$ok = $ok && padAiStore ( 'json' ) instanceof stdClass;
padAi ( 'Ordinary text please', [ 'provider' => 'ollama' ] );
$ok = $ok && padAiStore ( 'json' ) === NULL;
$answer = $ok ? 'JSON contracts isolate cache keys and decoded state' : 'FAIL';

?>
