<?php

  $schema = '{"type":"object","properties":{"ok":{"type":"boolean"}},"required":["ok"],"additionalProperties":false}';
  padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'content' => '{"ok":true}' ] ] ] ] );
  $text = padAi ( 'JSON please', [ 'provider' => 'ollama', 'schema' => $schema ] );
  $ok = $text === '{"ok":true}' && padAiStore ( 'json' )->ok === TRUE;
  $padCheckSyntax = FALSE;
  foreach ( [ '[]', '{bad}', '{"unsupported":true}' ] as $schema ) {
    padCurlFake ( [] );
    $text = padAi ( 'JSON please', [ 'provider' => 'ollama', 'schema' => $schema ] );
    $ok = $ok && $text === '' && count ( padCurlRecorded () ) === 0 && padAiError () !== '';
  }
  $answer = $ok ? 'JSON string schemas preserve the validation contract' : 'FAIL';

?>
