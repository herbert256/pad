<?php

  // OLLAMA_HOST is read as Ollama's own client reads it: host:port without a scheme - the
  // form Ollama documents, OLLAMA_HOST=0.0.0.0:11434 - is http, a host without a port gets
  // 11434, and 0.0.0.0, the address the server listens on, is reached as 127.0.0.1. An
  // address with its scheme is taken as it is.

  $padCheckSyntax = FALSE;

  padCurlFake ( [ '*' => [ 'data' => [ 'message' => [ 'role' => 'assistant', 'content' => 'Hi.' ] ] ] ] );

  $was    = getenv ( 'OLLAMA_HOST' );
  $asked  = [];

  try {

    foreach ( [ '', '127.0.0.1:11434', '0.0.0.0:11434', '0.0.0.0', 'ollama.lan', ':11500', 'http://localhost:11434/', 'https://ollama.example.com' ] as $host ) {
      putenv ( $host === '' ? 'OLLAMA_HOST' : "OLLAMA_HOST=$host" );
      $answer = padAi ( 'Hello?', [ 'provider' => 'ollama', 'ttl' => 0 ] );
      $calls  = padCurlRecorded ();
      $asked [ $host === '' ? '(not set)' : $host ] = $answer === '' ? padAiError () : ( end ( $calls ) ['url'] ?? '' );
    }

  } finally {

    putenv ( $was === FALSE ? 'OLLAMA_HOST' : "OLLAMA_HOST=$was" );

  }

  $json = json_encode ( $asked, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
