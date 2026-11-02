<?php

  // The ai subsystem: one prompt to one provider, for padAi (lib/ai.php) and the {pad:ai} tag. A
  // provider is a file of subs/ai/types/, answering an array:
  //
  //   name     its name in a message          key      the padEnv name of its API key, ''
  //                                                     - or a list of names, the first set
  //                                                     one read, the first named in a message
  //   optional TRUE when the key is sent only when there is one - a local server that asks for
  //            a token only when it is set up to
  //   model    the model unless model= says   tokens   the answer's limit unless tokens= says
  //   url      the endpoint, or a function of the request that makes it
  //   headers  function ( $ask ) - the headers: the key in the provider's own way
  //   body     function ( $ask ) - the request, sent as JSON
  //   text     function ( $answer ) - the text taken out of the answer, '' when there is none
  //   error    function ( $answer, $status, $headers ) - the error taken out of a failed answer,
  //            the answer's headers there for a provider that says the kind of error in one
  //   check    function ( $ask, $url ) - optional: the author's mistake the provider finds in
  //            what it is asked - a setting its address needs (an account) that is missing or
  //            wrong, a model name of its own form - '' when there is none; $url is the url=
  //            option, NULL when the provider's own address is used
  //
  // $ask is what the request is made of: provider, model, system, prompt, tokens, key. The
  // fetch is padCurl's - a test answers it with padCurlFake - and waits two minutes at most.

  include_once PAD . 'subs/ai/_lib/json.php';

  function padAiAsk ( $prompt, $options, $function ) {

    padAiStore ( 'error', '' );
    padAiStore ( 'json', NULL );

    if ( ! is_array ( $options ) )
      return padAiAuthor ( "$function: the options are an array - [ 'provider' => 'anthropic' ] - not " . get_debug_type ( $options ) );

    $schema = $options ['schema'] ?? [];
    if ( array_key_exists ( 'schema', $options ) && is_string ( $options ['schema'] ) ) {
      try {
        $object = json_decode ( $options ['schema'], FALSE, 64, JSON_THROW_ON_ERROR );
        if ( ! ( $object instanceof stdClass ) )
          return padAiAuthor ( "$function: schema text must be a JSON object" );
        $schema = json_decode ( $options ['schema'], TRUE, 64, JSON_THROW_ON_ERROR );
        $options ['schema'] = $schema;
      } catch ( JsonException $error ) {
        return padAiAuthor ( "$function: schema text must be a valid JSON object" );
      }
    }
    $json = $options ['json'] ?? FALSE;
    if ( ! is_bool ( $json ) && $json !== 0 && $json !== 1 )
      return padAiAuthor ( "$function: json must be true or false" );
    if ( array_key_exists ( 'schema', $options ) ) {
      if ( ( $error = padAiJsonSchemaError ( $options ['schema'] ) ) !== '' )
        return padAiAuthor ( "$function: $error" );
      $json = TRUE;
    }

    $provider = $options ['provider'] ?? padEnv ( 'PAD_AI_PROVIDER', 'anthropic' );
    $provider = is_string ( $provider ) ? strtolower ( trim ( $provider ) ) : '';

    if ( ! padKind ( 'ai', $provider ) )
      return padAiAuthor ( "$function: there is no AI provider named '" . padMakeSafe ( $provider, 30 ) . "' - " . implode ( ', ', padKinds ( 'ai' ) ) );

    $kind = ( function ( $padAiFile ) {
      $answer = include $padAiFile;
      return is_array ( $answer ) ? $answer : [];
    } ) ( PAD . "subs/ai/types/$provider.php" );

    $key = '';

    $names = array_values ( array_filter ( (array) ( $kind ['key'] ?? '' ), fn ( $name ) => is_string ( $name ) and $name !== '' ) );

    if ( $names ) {
      $key = $options ['key'] ?? NULL;
      foreach ( $key === NULL ? $names : [] as $name )
        if ( is_string ( $key = padEnv ( $name, '' ) ) and trim ( $key ) !== '' )
          break;
      if ( ! is_string ( $key ) or trim ( $key ) === '' ) {
        if ( empty ( $kind ['optional'] ) )
          return padAiAuthor ( "$function: " . $kind ['name'] . ' needs an API key - set ' . $names [0] . " in the application's .env, or give the option key" );
        $key = '';
      }
    }

    $tokens = $options ['tokens'] ?? $kind ['tokens'] ?? 4096;
    $ttl    = $options ['ttl'] ?? 3600;
    $model  = $options ['model'] ?? $kind ['model'] ?? '';
    $system = $options ['system'] ?? '';

    if ( ! is_numeric ( $tokens ) or (int) $tokens != $tokens or $tokens < 1 or $tokens > 1000000 )
      return padAiAuthor ( "$function: tokens is the most the answer may take, a whole number above 0 - not " . padMakeSafe ( is_scalar ( $tokens ) ? (string) $tokens : get_debug_type ( $tokens ), 20 ) );

    if ( ! is_numeric ( $ttl ) or (int) $ttl != $ttl or $ttl < 0 )
      return padAiAuthor ( "$function: ttl is the seconds an answer is kept, 0 or more - not " . padMakeSafe ( is_scalar ( $ttl ) ? (string) $ttl : get_debug_type ( $ttl ), 20 ) );

    if ( ! is_string ( $model ) or ! preg_match ( '/^@?[A-Za-z0-9][A-Za-z0-9._:\/-]{0,127}$/D', $model ) )
      return padAiAuthor ( "$function: the model is a name like " . ( $kind ['model'] ?? 'its own' ) . ' - not ' . padMakeSafe ( is_scalar ( $model ) ? (string) $model : get_debug_type ( $model ), 40 ) );

    if ( ! is_scalar ( $system ) or ! is_scalar ( $prompt ) or trim ( (string) $prompt ) === '' )
      return padAiAuthor ( "$function: there is no prompt to send - the question, as text" );

    $ask = [ 'provider' => $provider, 'model' => $model, 'system' => trim ( (string) $system ), 'prompt' => trim ( (string) $prompt ),
             'tokens'   => (int) $tokens, 'key' => trim ( $key ), 'json' => (bool) $json, 'schema' => $schema ];

    if ( ( $kind ['check'] ?? NULL ) instanceof Closure and ( $said = (string) ( $kind ['check'] ) ( $ask, $options ['url'] ?? NULL ) ) !== '' )
      return padAiAuthor ( "$function: $said" );

    $url = $options ['url'] ?? $kind ['url'];
    $url = ( $url instanceof Closure ) ? $url ( $ask ) : (string) $url;

    if ( ! preg_match ( '~^https?://[^\s/?#]+\S*$~iD', $url ) )
      return padAiAuthor ( "$function: the url is the address of the API, http or https - not " . padMakeSafe ( $url, 60 ) );

    // The same question is the same answer: kept under a hash of what was asked, the key left
    // out - another key asks the same model the same thing.

    $cache = padAiCacheKey ( $ask, $url );
    $keep  = ( (int) $ttl > 0 and ! padCurlFaking () );

    if ( $keep ) {
      $hit = padCacheGet ( $cache );
      if ( is_string ( $hit ) and $hit !== '' ) {
        if ( ! $json || padAiJsonAnswer ( $hit, $schema ) === '' )
          return $hit;
        padCacheForget ( $cache );
      }
    }

    $out = padCurl ( [ 'url'     => $url,
                       'post'    => json_encode ( ( $kind ['body'] ) ( $ask ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
                       'headers' => ( $kind ['headers'] ) ( $ask ) + [ 'Content-Type' => 'application/json', 'Accept' => 'application/json' ],
                       'options' => [ 'USERAGENT' => 'PAD', 'TIMEOUT' => 120 ] ] );

    $status = (int) ( $out ['result'] ?? 0 );
    $answer = json_decode ( (string) ( $out ['data'] ?? '' ), TRUE );
    $answer = is_array ( $answer ) ? $answer : [];
    $text   = ( $status >= 200 and $status <= 299 ) ? ( $kind ['text'] ) ( $answer ) : '';
    $text   = is_string ( $text ) ? trim ( $text ) : '';

    if ( $text === '' ) {

      // What the provider said, as text - a server behind url= may answer a message that is a
      // list or an object (a validation error's location and text): its JSON then.

      $said = ( $kind ['error'] ) ( $answer, $status, (array) ( $out ['headers'] ?? [] ) );
      $said = is_scalar ( $said ) ? trim ( (string) $said )
                                  : ( $said === NULL ? '' : (string) json_encode ( $said, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) );

      if ( $said === '' )
        $said = isset ( $out ['ERROR'] ) ? 'no answer' : ( ( $status >= 200 and $status <= 299 ) ? 'an answer without text' : "HTTP $status" );

      return padAiFail ( $kind ['name'] . ' (' . $model . ') did not answer: ' . padMakeSafe ( $said, 300 ), $provider, $model, $status );

    }

    if ( $json && ( $error = padAiJsonAnswer ( $text, $schema ) ) !== '' )
      return padAiFail ( $kind ['name'] . ' (' . $model . ') returned invalid structured data: ' . $error, $provider, $model, $status );

    if ( $keep )
      padCachePut ( $cache, $text, (int) $ttl );

    return $text;

  }

  function padAiCacheKey ( $ask, $url ) {

    return 'pad-ai:' . hash ( 'sha256', json_encode ( [ $ask ['provider'], $ask ['model'], $ask ['system'], $ask ['prompt'], $ask ['tokens'], $url, $ask ['json'] ?? FALSE, $ask ['schema'] ?? [] ] ) );

  }

  // An error of the API: logged, kept for padAiError, and an error under the strict check.

  function padAiFail ( $message, $provider, $model, $status ) {

    padAiStore ( 'error', $message );

    padLog ( '{message}', 'warning', [ 'message' => $message, 'provider' => $provider, 'model' => $model, 'status' => $status ] );

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );

    return '';

  }

  // An author's mistake - a missing key, an unknown provider, a wrong option: an error under
  // the strict check, else logged as an error of the API is, the page going on.

  function padAiAuthor ( $message ) {

    padAiStore ( 'error', $message );

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );
    else
      padLog ( '{message}', 'warning', [ 'message' => $message ] );

    return '';

  }

?>
