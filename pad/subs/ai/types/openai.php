<?php

  // openai - OpenAI's chat completions: the key in OPENAI_API_KEY, gpt-5 unless model= names
  // another. The system prompt is the first message, the prompt the user's; the answer is the
  // first choice's message. url= points it at another server that speaks the same API.

  return [
    'name'    => 'OpenAI',
    'capabilities' => ['nativeJson'=>TRUE,'nativeSchema'=>TRUE],
    'nativeProtocol' => 'openai',
    'key'     => 'OPENAI_API_KEY',
    'model'   => 'gpt-5',
    'tokens'  => 16000,
    'url'     => 'https://api.openai.com/v1/chat/completions',
    'headers' => fn ( $ask ) => [ 'Authorization' => 'Bearer ' . $ask ['key'] ],
    'body'    => fn ( $ask ) => [ 'model'                 => $ask ['model'],
                                  'messages'              => array_merge ( $ask ['system'] !== '' ? [ [ 'role' => 'system', 'content' => $ask ['system'] ] ] : [],
                                                                           [ [ 'role' => 'user', 'content' => $ask ['prompt'] ] ] ),
                                  'max_completion_tokens' => $ask ['tokens'] ],
    'text'    => fn ( $answer ) => is_string ( $answer ['choices'] [0] ['message'] ['content'] ?? NULL ) ? $answer ['choices'] [0] ['message'] ['content'] : '',
    'error'   => fn ( $answer ) => $answer ['error'] ['message'] ?? ( isset ( $answer ['choices'] [0] ['message'] ['refusal'] ) ? 'the model declined to answer: ' . $answer ['choices'] [0] ['message'] ['refusal'] : '' ),
  ];

?>
