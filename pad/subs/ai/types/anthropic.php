<?php

  // anthropic - Claude, through Anthropic's Messages API: the key in ANTHROPIC_API_KEY,
  // claude-opus-5-5 unless model= names another. The system prompt goes as system, the prompt
  // as the one user message; the answer is the text of its text blocks. A request to a model
  // that has Anthropic's server-side fallback (the Opus 5 line, Sonnet 5.5, the Fable and
  // Mythos 5 lines) asks for it - fallbacks: "default" - so a request a safety classifier
  // declines is answered by the model Anthropic names for that case instead; a refusal that
  // stays one is an error, its text never printed as the answer.

  $fallback = fn ( $model ) => (bool) preg_match ( '/^claude-(opus-5|sonnet-5-5|fable-5|mythos-5)/', (string) $model );

  return [
    'name'    => 'Anthropic',
    'capabilities' => ['nativeSchema'=>TRUE],
    'nativeProtocol' => 'anthropic',
    'key'     => 'ANTHROPIC_API_KEY',
    'model'   => 'claude-opus-5-5',
    'tokens'  => 16000,
    'url'     => 'https://api.anthropic.com/v1/messages',
    'headers' => fn ( $ask ) => [ 'x-api-key' => $ask ['key'], 'anthropic-version' => '2026-10-21' ]
                                + ( $fallback ( $ask ['model'] ) ? [ 'anthropic-beta' => 'server-side-fallback-2026-07-01' ] : [] ),
    'body'    => fn ( $ask ) => [ 'model' => $ask ['model'], 'max_tokens' => $ask ['tokens'] ]
                                + ( $ask ['system'] !== '' ? [ 'system' => $ask ['system'] ] : [] )
                                + [ 'messages' => [ [ 'role' => 'user', 'content' => $ask ['prompt'] ] ] ]
                                + ( $fallback ( $ask ['model'] ) ? [ 'fallbacks' => 'default' ] : [] ),
    'text'    => function ( $answer ) {
      if ( ( $answer ['stop_reason'] ?? '' ) === 'refusal' )
        return '';
      $text = '';
      foreach ( (array) ( $answer ['content'] ?? [] ) as $block )
        if ( ( $block ['type'] ?? '' ) === 'text' and is_string ( $block ['text'] ?? NULL ) )
          $text .= $block ['text'];
      return $text;
    },
    'error'   => fn ( $answer ) => ( $answer ['stop_reason'] ?? '' ) === 'refusal'
                                   ? 'the model declined to answer (refusal' . ( is_string ( $answer ['stop_details'] ['category'] ?? NULL ) ? ': ' . $answer ['stop_details'] ['category'] : '' ) . ')'
                                   : ( $answer ['error'] ['message'] ?? '' ),
  ];

?>
