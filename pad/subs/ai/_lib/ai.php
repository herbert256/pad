<?php

  // The ai subsystem: one prompt to one provider, for padAi (lib/ai.php) and the {ai} tag. A
  // provider is a file of subs/ai/types/, answering an array:
  //
  //   name     its name in a message          key      the padEnv name of its API key, ''
  //   model    the model unless model= says   tokens   the answer's limit unless tokens= says
  //   url      the endpoint, or a function of the request that makes it
  //   headers  function ( $ask ) - the headers: the key in the provider's own way
  //   body     function ( $ask ) - the request, sent as JSON
  //   text     function ( $answer ) - the text taken out of the answer, '' when there is none
  //   error    function ( $answer, $status ) - the error taken out of a failed answer
  //
  // $ask is what the request is made of: provider, model, system, prompt, tokens, key. The
  // fetch is padCurl's - a test answers it with padCurlFake - and waits two minutes at most.

  function padAiAsk ( $prompt, $options, $function ) {

    padAiStore ( 'error', '' );

    if ( ! is_array ( $options ) )
      return padAiAuthor ( "$function: the options are an array - [ 'provider' => 'anthropic' ] - not " . get_debug_type ( $options ) );

    $provider = $options ['provider'] ?? padEnv ( 'PAD_AI_PROVIDER', 'anthropic' );
    $provider = is_string ( $provider ) ? strtolower ( trim ( $provider ) ) : '';

    if ( ! padKind ( 'ai', $provider ) )
      return padAiAuthor ( "$function: there is no AI provider named '" . padMakeSafe ( $provider, 30 ) . "' - " . implode ( ', ', padKinds ( 'ai' ) ) );

    $kind = ( function ( $padAiFile ) {
      $answer = include $padAiFile;
      return is_array ( $answer ) ? $answer : [];
    } ) ( PAD . "subs/ai/types/$provider.php" );

    $key = '';

    if ( ( $kind ['key'] ?? '' ) !== '' ) {
      $key = $options ['key'] ?? padEnv ( $kind ['key'], '' );
      if ( ! is_string ( $key ) or trim ( $key ) === '' )
        return padAiAuthor ( "$function: " . $kind ['name'] . ' needs an API key - set ' . $kind ['key'] . " in the application's .env, or give the option key" );
    }

    $tokens = $options ['tokens'] ?? $kind ['tokens'] ?? 4096;
    $ttl    = $options ['ttl'] ?? 3600;
    $model  = $options ['model'] ?? $kind ['model'] ?? '';
    $system = $options ['system'] ?? '';

    if ( ! is_numeric ( $tokens ) or (int) $tokens != $tokens or $tokens < 1 or $tokens > 1000000 )
      return padAiAuthor ( "$function: tokens is the most the answer may take, a whole number above 0 - not " . padMakeSafe ( is_scalar ( $tokens ) ? (string) $tokens : get_debug_type ( $tokens ), 20 ) );

    if ( ! is_numeric ( $ttl ) or (int) $ttl != $ttl or $ttl < 0 )
      return padAiAuthor ( "$function: ttl is the seconds an answer is kept, 0 or more - not " . padMakeSafe ( is_scalar ( $ttl ) ? (string) $ttl : get_debug_type ( $ttl ), 20 ) );

    if ( ! is_string ( $model ) or ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9._:\/-]{0,127}$/D', $model ) )
      return padAiAuthor ( "$function: the model is a name like " . ( $kind ['model'] ?? 'its own' ) . ' - not ' . padMakeSafe ( is_scalar ( $model ) ? (string) $model : get_debug_type ( $model ), 40 ) );

    if ( ! is_scalar ( $system ) or ! is_scalar ( $prompt ) or trim ( (string) $prompt ) === '' )
      return padAiAuthor ( "$function: there is no prompt to send - the question, as text" );

    $ask = [ 'provider' => $provider, 'model' => $model, 'system' => trim ( (string) $system ), 'prompt' => trim ( (string) $prompt ),
             'tokens'   => (int) $tokens, 'key' => trim ( $key ) ];

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
      if ( is_string ( $hit ) and $hit !== '' )
        return $hit;
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

      $said = trim ( (string) ( ( $kind ['error'] ) ( $answer, $status ) ) );

      if ( $said === '' )
        $said = isset ( $out ['ERROR'] ) ? 'no answer' : ( ( $status >= 200 and $status <= 299 ) ? 'an answer without text' : "HTTP $status" );

      return padAiFail ( $kind ['name'] . ' (' . $model . ') did not answer: ' . padMakeSafe ( $said, 300 ), $provider, $model, $status );

    }

    if ( $keep )
      padCachePut ( $cache, $text, (int) $ttl );

    return $text;

  }

  function padAiCacheKey ( $ask, $url ) {

    return 'pad-ai:' . hash ( 'sha256', json_encode ( [ $ask ['provider'], $ask ['model'], $ask ['system'], $ask ['prompt'], $ask ['tokens'], $url ] ) );

  }

  // An error of the API: logged, kept for padAiError, and an error under the strict check.

  function padAiFail ( $message, $provider, $model, $status ) {

    padAiStore ( 'error', $message );

    padLog ( '{message}', 'warning', [ 'message' => $message, 'provider' => $provider, 'model' => $model, 'status' => $status ] );

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );

    return '';

  }

  function padAiAuthor ( $message ) {

    padAiStore ( 'error', $message );

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );

    return '';

  }

?>
