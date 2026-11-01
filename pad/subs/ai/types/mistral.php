<?php

  // mistral - Mistral's chat completions: the key in MISTRAL_API_KEY, mistral-large-latest
  // unless model= names another. The system prompt is the first message, the prompt the
  // user's; the answer is the first choice's message - its text chunks joined when it comes
  // in chunks.

  return [
    'name'    => 'Mistral',
    'key'     => 'MISTRAL_API_KEY',
    'model'   => 'mistral-large-latest',
    'tokens'  => 4096,
    'url'     => 'https://api.mistral.ai/v1/chat/completions',
    'headers' => fn ( $ask ) => [ 'Authorization' => 'Bearer ' . $ask ['key'] ],
    'body'    => fn ( $ask ) => [ 'model'      => $ask ['model'],
                                  'messages'   => array_merge ( $ask ['system'] !== '' ? [ [ 'role' => 'system', 'content' => $ask ['system'] ] ] : [],
                                                                [ [ 'role' => 'user', 'content' => $ask ['prompt'] ] ] ),
                                  'max_tokens' => $ask ['tokens'] ],
    'text'    => function ( $answer ) {
      $content = $answer ['choices'] [0] ['message'] ['content'] ?? '';
      if ( is_array ( $content ) )
        $content = implode ( '', array_map ( fn ( $chunk ) => ( $chunk ['type'] ?? '' ) === 'text' ? (string) ( $chunk ['text'] ?? '' ) : '', $content ) );
      return is_string ( $content ) ? $content : '';
    },
    'error'   => function ( $answer ) {
      $said = $answer ['message'] ?? $answer ['detail'] ?? $answer ['error'] ['message'] ?? '';
      return is_array ( $said ) ? (string) ( $said [0] ['msg'] ?? json_encode ( $said ) ) : (string) $said;
    },
  ];

?>
