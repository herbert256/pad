<?php

  // cloudflare - Workers AI, the models Cloudflare runs on its own network, through its REST
  // API (developers.cloudflare.com/workers-ai/get-started/rest-api/): POST
  // https://api.cloudflare.com/client/v4/accounts/<account>/ai/run/<model> with the API token
  // as a bearer token - the token in CLOUDFLARE_API_TOKEN, the account in CLOUDFLARE_ACCOUNT_ID,
  // the names Cloudflare's Wrangler reads - @cf/meta/llama-3.3-70b-instruct-fp8-fast unless
  // model= names another of the catalogue's text generation models (@cf/<author>/<name>,
  // @hf/... - the model is a part of the address, so a name of another form is refused). The
  // system prompt is the first message, the prompt the user's, max_tokens the limit (256 when
  // not sent), asked without streaming. The answer is the response of its result - or, for
  // the models that answer in the form of a chat completion (Qwen3 and others), the first
  // choice's message. A failure says itself in errors, each a message and a code - 3036 the
  // day's free allocation used up, 3040 out of capacity, both 429, 5007 no such model. url=
  // names another address of the same API, an AI Gateway's: the account is not needed then.

  $account = fn () => trim ( (string) padEnv ( 'CLOUDFLARE_ACCOUNT_ID', '' ) );

  return [
    'name'    => 'Cloudflare Workers AI',
    'key'     => 'CLOUDFLARE_API_TOKEN',
    'model'   => '@cf/meta/llama-3.3-70b-instruct-fp8-fast',
    'tokens'  => 4096,
    'check'   => function ( $ask, $url ) use ( $account ) {
      if ( ! preg_match ( '~^@[a-z0-9-]+/[A-Za-z0-9._-]+/[A-Za-z0-9._-]+$~D', $ask ['model'] ) or preg_match ( '~(^|/)\.{1,2}(/|$)~', $ask ['model'] ) )
        return 'the model of Cloudflare Workers AI is a name like @cf/meta/llama-3.3-70b-instruct-fp8-fast - not ' . padMakeSafe ( $ask ['model'], 60 );
      if ( $url !== NULL )
        return '';
      if ( $account () === '' )
        return "Cloudflare Workers AI needs the account its models run under - set CLOUDFLARE_ACCOUNT_ID in the application's .env";
      if ( ! preg_match ( '/^[0-9a-f]{32}$/iD', $account () ) )
        return 'the CLOUDFLARE_ACCOUNT_ID of Cloudflare Workers AI is the 32 hexadecimal characters of the account id - not ' . padMakeSafe ( $account (), 40 );
      return '';
    },
    'url'     => fn ( $ask ) => 'https://api.cloudflare.com/client/v4/accounts/' . strtolower ( $account () ) . '/ai/run/' . $ask ['model'],
    'headers' => fn ( $ask ) => [ 'Authorization' => 'Bearer ' . $ask ['key'] ],
    'body'    => fn ( $ask ) => [ 'messages'   => array_merge ( $ask ['system'] !== '' ? [ [ 'role' => 'system', 'content' => $ask ['system'] ] ] : [],
                                                                [ [ 'role' => 'user', 'content' => $ask ['prompt'] ] ] ),
                                  'max_tokens' => $ask ['tokens'] ],
    'text'    => function ( $answer ) {
      if ( ( $answer ['success'] ?? TRUE ) === FALSE )
        return '';
      $result = $answer ['result'] ?? NULL;
      if ( is_string ( $result ) )
        return $result;
      if ( is_string ( $result ['response'] ?? NULL ) )
        return $result ['response'];
      $content = $result ['choices'] [0] ['message'] ['content'] ?? NULL;
      return is_string ( $content ) ? $content : '';
    },
    'error'   => function ( $answer ) {
      $said = [];
      foreach ( (array) ( $answer ['errors'] ?? [] ) as $error )
        if ( is_array ( $error ) and is_scalar ( $error ['message'] ?? NULL ) )
          $said [] = $error ['message'] . ( is_scalar ( $error ['code'] ?? NULL ) ? ' (' . $error ['code'] . ')' : '' );
      return implode ( '; ', $said );
    },
  ];

?>
