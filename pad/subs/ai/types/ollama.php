<?php

  // ollama - a model running on this machine or the network with Ollama: no key, the server
  // at OLLAMA_HOST (http://127.0.0.1:11434 unless set), llama3.2 unless model= names another -
  // a model pulled there first (ollama pull llama3.2). The answer is the message of /api/chat,
  // asked without streaming. OLLAMA_HOST is read as Ollama's own client reads it: host:port
  // without a scheme - OLLAMA_HOST=0.0.0.0:11434, as Ollama documents it - is http, a host
  // without a port gets 11434, and 0.0.0.0, the address the server listens on, is reached as
  // 127.0.0.1; an address with its scheme is taken as it is.

  $host = function () {
    $host = trim ( (string) padEnv ( 'OLLAMA_HOST', '' ) );
    if ( ! preg_match ( '~^[a-z][a-z0-9+.-]*://~i', $host ) ) {
      [ $name, $path ] = array_pad ( explode ( '/', $host, 2 ), 2, '' );
      if ( ! preg_match ( '~^(\[[^\]]*\]|[^:]*)(:\d+)?$~', $name, $match ) )
        return "http://$host";
      $host = 'http://' . ( $match [1] === '' ? '127.0.0.1' : $match [1] ) . ( $match [2] ?? '' ?: ':11434' ) . ( $path === '' ? '' : "/$path" );
    }
    return preg_replace ( [ '~^(https?://)0\.0\.0\.0(?=[:/]|$)~i', '~^(https?://)\[::\](?=[:/]|$)~i' ], [ '${1}127.0.0.1', '${1}[::1]' ], $host );
  };

  return [
    'name'    => 'Ollama',
    'capabilities' => ['nativeJson'=>TRUE,'nativeSchema'=>TRUE],
    'nativeProtocol' => 'ollama',
    'key'     => '',
    'model'   => 'llama3.2',
    'tokens'  => 4096,
    'url'     => fn ( $ask ) => rtrim ( $host (), '/' ) . '/api/chat',
    'headers' => fn ( $ask ) => [],
    'body'    => fn ( $ask ) => [ 'model'    => $ask ['model'],
                                  'messages' => array_merge ( $ask ['system'] !== '' ? [ [ 'role' => 'system', 'content' => $ask ['system'] ] ] : [],
                                                              [ [ 'role' => 'user', 'content' => $ask ['prompt'] ] ] ),
                                  'stream'   => FALSE,
                                  'options'  => [ 'num_predict' => $ask ['tokens'] ] ],
    'text'    => fn ( $answer ) => is_string ( $answer ['message'] ['content'] ?? NULL ) ? $answer ['message'] ['content'] : '',
    'error'   => fn ( $answer ) => is_string ( $answer ['error'] ?? NULL ) ? $answer ['error'] : '',
  ];

?>
