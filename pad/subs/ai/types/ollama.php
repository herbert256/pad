<?php

  // ollama - a model running on this machine or the network with Ollama: no key, the server
  // at OLLAMA_HOST (http://127.0.0.1:11434 unless set), llama3.2 unless model= names another -
  // a model pulled there first (ollama pull llama3.2). The answer is the message of /api/chat,
  // asked without streaming.

  return [
    'name'    => 'Ollama',
    'key'     => '',
    'model'   => 'llama3.2',
    'tokens'  => 4096,
    'url'     => fn ( $ask ) => rtrim ( (string) padEnv ( 'OLLAMA_HOST', 'http://127.0.0.1:11434' ), '/' ) . '/api/chat',
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
