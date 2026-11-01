<?php

  // cohere - Cohere's Chat API v2 (POST https://api.cohere.com/v2/chat, docs.cohere.com/reference/chat):
  // the key in COHERE_API_KEY - or CO_API_KEY, the name Cohere's own SDKs read - sent as a
  // bearer token, command-a-plus-05-2026 unless model= names another. The system prompt is the
  // first message, the prompt the user's, max_tokens the limit, asked without streaming. The
  // answer is the message's content - a list of blocks: the text blocks are joined, a thinking
  // block (a reasoning model's) is passed over, any other block is no text. An answer that
  // finished with ERROR or TIMEOUT is an error, its partial text never printed; an error answer
  // - 400, 401, 404, 422, 429 Too Many Requests, 498, 5xx - says itself in message.

  return [
    'name'    => 'Cohere',
    'key'     => [ 'COHERE_API_KEY', 'CO_API_KEY' ],
    'model'   => 'command-a-plus-05-2026',
    'tokens'  => 4096,
    'url'     => 'https://api.cohere.com/v2/chat',
    'headers' => fn ( $ask ) => [ 'Authorization' => 'Bearer ' . $ask ['key'] ],
    'body'    => fn ( $ask ) => [ 'model'      => $ask ['model'],
                                  'messages'   => array_merge ( $ask ['system'] !== '' ? [ [ 'role' => 'system', 'content' => $ask ['system'] ] ] : [],
                                                                [ [ 'role' => 'user', 'content' => $ask ['prompt'] ] ] ),
                                  'max_tokens' => $ask ['tokens'] ],
    'text'    => function ( $answer ) {
      if ( in_array ( strtoupper ( (string) ( $answer ['finish_reason'] ?? '' ) ), [ 'ERROR', 'TIMEOUT' ], TRUE ) )
        return '';
      $text = '';
      foreach ( (array) ( $answer ['message'] ['content'] ?? [] ) as $block )
        if ( ( $block ['type'] ?? '' ) === 'text' and is_string ( $block ['text'] ?? NULL ) )
          $text .= $block ['text'];
      return $text;
    },
    'error'   => function ( $answer ) {
      $reason = strtoupper ( (string) ( $answer ['finish_reason'] ?? '' ) );
      if ( in_array ( $reason, [ 'ERROR', 'TIMEOUT' ], TRUE ) )
        return 'the answer ended in ' . $reason;
      return is_string ( $answer ['message'] ?? NULL ) ? $answer ['message'] : '';
    },
  ];

?>
