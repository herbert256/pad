<?php

  // gemini - Google's Gemini API, generateContent: the key in GEMINI_API_KEY (sent as the
  // x-goog-api-key header, never in the address), gemini-2.5-flash unless model= names another.
  // The system prompt is the systemInstruction, the prompt the user's content; the answer is
  // the text of the first candidate's parts, its thought parts left out. A prompt Gemini
  // blocks is an error that names the reason.

  return [
    'name'    => 'Gemini',
    'key'     => 'GEMINI_API_KEY',
    'model'   => 'gemini-2.5-flash',
    'tokens'  => 8192,
    'url'     => fn ( $ask ) => 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode ( $ask ['model'] ) . ':generateContent',
    'headers' => fn ( $ask ) => [ 'x-goog-api-key' => $ask ['key'] ],
    'body'    => fn ( $ask ) => ( $ask ['system'] !== '' ? [ 'systemInstruction' => [ 'parts' => [ [ 'text' => $ask ['system'] ] ] ] ] : [] )
                                + [ 'contents'         => [ [ 'role' => 'user', 'parts' => [ [ 'text' => $ask ['prompt'] ] ] ] ],
                                    'generationConfig' => [ 'maxOutputTokens' => $ask ['tokens'] ] ],
    'text'    => function ( $answer ) {
      $text = '';
      foreach ( (array) ( $answer ['candidates'] [0] ['content'] ['parts'] ?? [] ) as $part )
        if ( empty ( $part ['thought'] ) and is_string ( $part ['text'] ?? NULL ) )
          $text .= $part ['text'];
      return $text;
    },
    'error'   => fn ( $answer ) => $answer ['error'] ['message']
                                   ?? ( isset ( $answer ['promptFeedback'] ['blockReason'] ) ? 'the prompt was blocked: ' . $answer ['promptFeedback'] ['blockReason'] : '' ),
  ];

?>
