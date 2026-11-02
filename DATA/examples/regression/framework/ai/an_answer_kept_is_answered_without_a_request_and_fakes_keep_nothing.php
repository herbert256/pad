<?php

  // An answer is kept (padCachePut) under a hash of the provider, model, system prompt, prompt,
  // tokens and address: the same question again is answered from there, no request made - the
  // address here is a closed port of this machine, so a request that went out would fail. While
  // the fetches are faked nothing is read or kept: the same question is fetched twice, and the
  // fake's answer is not left behind for the real requests after the test.

  include_once PAD . 'subs/ai/_lib/ai.php';

  $_ENV ['MISTRAL_API_KEY'] = 'sk-test';

  $url = 'http://127.0.0.1:9/v1/chat/completions';
  $key = padAiCacheKey ( [ 'provider' => 'mistral', 'model' => 'mistral-large-latest', 'system' => '', 'prompt' => 'Kept?', 'tokens' => 4096 ], $url );

  padCachePut ( $key, 'Yes, kept.', 60 );

  $kept = padAi ( 'Kept?', [ 'provider' => 'mistral', 'url' => $url ] );

  padCacheForget ( $key );

  padCurlFake ( [ 'http://127.0.0.1:9/*' => [ 'data' => [ 'choices' => [ [ 'message' => [ 'content' => 'Fresh.' ] ] ] ] ] ] );

  $one     = padAi ( 'Kept?', [ 'provider' => 'mistral', 'url' => $url ] );
  $two     = padAi ( 'Kept?', [ 'provider' => 'mistral', 'url' => $url ] );
  $fetched = count ( padCurlRecorded () );
  $after   = padCacheHas ( $key ) ? 'kept' : 'not kept';

?>
