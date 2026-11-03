<?php

  padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'content' => '{"label":"<b>safe</b> & {php:getcwd}","ok":true}' ] ] ] ] );
  $schema = '{"type":"object","properties":{"label":{"type":"string"},"ok":{"type":"boolean"}},"required":["label","ok"],"additionalProperties":false}';
  $single = padUnprotect ( padUnescape ( padCode ( "{ai:ollama 'Give JSON', schema='$schema'/}" ) ) );
  $pair = padUnprotect ( padUnescape ( padCode ( "{ai:auto provider='ollama', json}Give JSON: {pad:echo 'question'/}{/ai:auto}" ) ) );
  $calls = padCurlRecorded ();
  $prompt = json_decode ( $calls [1] ['post'], TRUE ) ['messages'] [0] ['content'];
  $ok = $single === $pair && str_contains ( $single, '&lt;b&gt;safe&lt;/b&gt;' ) && ! str_contains ( $single, '<b>' )
     && str_contains ( $single, '{php:getcwd}' ) && count ( $calls ) === 2 && $prompt === 'Give JSON: question'
     && padAiStore ( 'json' )->ok === TRUE;
  $answer = $ok ? 'native AI kinds validate JSON strings and preserve pair rendering and escaping' : 'FAIL';

?>
