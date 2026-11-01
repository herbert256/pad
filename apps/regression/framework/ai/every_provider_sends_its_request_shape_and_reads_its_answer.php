<?php

  // Every provider of subs/ai/types/, answered by a fake in its own shape: the address, the
  // header that carries the key, the body it sends for the same system prompt and prompt, and
  // the text it takes out of its answer - Anthropic's thinking block passed over, Gemini's
  // thought part too.

  padCurlFake ( [
    'https://api.anthropic.com/v1/messages' => [ 'data' => [ 'type' => 'message', 'stop_reason' => 'end_turn',
                                                              'content' => [ [ 'type' => 'thinking', 'thinking' => '' ], [ 'type' => 'text', 'text' => 'Paris.' ] ] ] ],
    'https://api.openai.com/v1/chat/completions' => [ 'data' => [ 'choices' => [ [ 'message' => [ 'role' => 'assistant', 'content' => 'Paris.' ] ] ] ] ],
    'https://api.mistral.ai/v1/chat/completions' => [ 'data' => [ 'choices' => [ [ 'message' => [ 'content' => [ [ 'type' => 'text', 'text' => 'Par' ], [ 'type' => 'text', 'text' => 'is.' ] ] ] ] ] ] ],
    'https://generativelanguage.googleapis.com/*' => [ 'data' => [ 'candidates' => [ [ 'content' => [ 'parts' => [ [ 'text' => 'hmm', 'thought' => TRUE ], [ 'text' => 'Paris.' ] ] ] ] ] ] ],
    'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'role' => 'assistant', 'content' => 'Paris.' ] ] ],
  ] );

  $providers = [];

  foreach ( padAiProviders () as $provider ) {

    $answer = padAi ( 'What is the capital of France?', [ 'provider' => $provider, 'key' => 'test-key', 'system' => 'Answer in one word.', 'tokens' => 50 ] );
    $call   = padCurlRecorded () [ count ( padCurlRecorded () ) - 1 ];

    $providers [$provider] = [
      'url'    => $call ['url'],
      'key in' => array_keys ( array_filter ( $call ['headers'], fn ( $value ) => str_contains ( $value, 'test-key' ) ) ),
      'extra'  => array_intersect_key ( $call ['headers'], [ 'anthropic-version' => 1, 'anthropic-beta' => 1 ] ),
      'body'   => json_decode ( $call ['post'], TRUE ),
      'answer' => $answer ];

  }

  $json = json_encode ( $providers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
