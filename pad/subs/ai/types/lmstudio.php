<?php

  // lmstudio - a model running on this machine or the network with LM Studio, through its
  // native REST API (lmstudio.ai/docs/developer/rest/chat, LM Studio 0.4 and later): POST
  // http://127.0.0.1:1234/api/v1/chat - the server LM Studio starts (lms server start), url=
  // naming another address - ibm/granite-4-micro unless model= names another downloaded model,
  // loaded on the first request when it is not. No key is needed: LM Studio asks for none
  // unless its Require Authentication setting is on, and then the token of LM_API_TOKEN - the
  // name of LM Studio's own examples - or key= goes as a bearer token. The prompt is the input,
  // the system prompt the system_prompt, max_output_tokens the limit, asked without streaming
  // and kept by nobody (store false). The answer is the content of the message items of its
  // output, joined - a reasoning item, a tool call are no text. An error is the message of its
  // error, as the server says it; a server that answers a wrong path with a 200 and an error,
  // as LM Studio has, is an error too: an answer without a message item is no answer.

  return [
    'name'     => 'LM Studio',
    'key'      => 'LM_API_TOKEN',
    'optional' => TRUE,
    'model'    => 'ibm/granite-4-micro',
    'tokens'   => 4096,
    'url'      => 'http://127.0.0.1:1234/api/v1/chat',
    'headers'  => fn ( $ask ) => $ask ['key'] !== '' ? [ 'Authorization' => 'Bearer ' . $ask ['key'] ] : [],
    'body'     => fn ( $ask ) => [ 'model' => $ask ['model'], 'input' => $ask ['prompt'] ]
                                 + ( $ask ['system'] !== '' ? [ 'system_prompt' => $ask ['system'] ] : [] )
                                 + [ 'max_output_tokens' => $ask ['tokens'], 'stream' => FALSE, 'store' => FALSE ],
    'text'     => function ( $answer ) {
      $text = '';
      foreach ( (array) ( $answer ['output'] ?? [] ) as $item )
        if ( ( $item ['type'] ?? '' ) === 'message' and is_string ( $item ['content'] ?? NULL ) )
          $text .= $item ['content'];
      return $text;
    },
    'error'    => function ( $answer ) {
      $error = $answer ['error'] ?? NULL;
      if ( is_array ( $error ) )
        return $error ['message'] ?? '';
      if ( is_string ( $error ) )
        return $error;
      return is_string ( $answer ['message'] ?? NULL ) ? $answer ['message'] : '';
    },
  ];

?>
