<?php

  // A language model answering in the page: a prompt sent to Anthropic, OpenAI, Mistral,
  // Gemini, Cohere, Cloudflare Workers AI, Amazon Bedrock or a local Ollama or LM Studio, the
  // text of the answer back. The providers are the files of subs/ai/types/ (padKinds): a
  // provider is added by adding its file.
  //
  //   $summary = padAi ( "Summarise in one sentence: $body", [ 'provider' => 'anthropic', 'ttl' => 86400 ] );
  //   {ai provider='anthropic', ttl=86400}Summarise in one sentence: {$body}{/ai}
  //
  // padAi           the answer to a prompt, as text - '' when there is none; the options:
  //                 provider (PAD_AI_PROVIDER, else anthropic), model (the provider's own
  //                 default), system (the system prompt), tokens (the most the answer may
  //                 take), ttl (seconds the answer is kept - 3600 unless given, 0 keeps
  //                 nothing), key (the API key, else the provider's variable - ANTHROPIC_API_KEY
  //                 and the like - read with padEnv) and url (another address of the same API)
  // padAiError      why the last padAi answered '', '' when it did not
  // padAiProviders  the providers there are
  //
  // An answer is kept with padCacheGet/padCachePut under a hash of the provider, the model,
  // the system prompt, the prompt and the tokens: the same question on the next request is
  // the same answer, without a request. A failed answer is never kept, and while a test fakes
  // the fetches (padCurlFake) nothing is read from or written to that cache.
  //
  // A missing key, an unknown provider or a wrong option is the author's: an error under the
  // strict check, the variable to set named - without it logged (padLog, warning), padAi
  // answering ''. An error of the API - a refused key, a model
  // that is not there, a limit, a refusal - is logged (padLog, warning) and is an error under
  // the strict check as well; without it padAi answers '' and the page goes on. The code is
  // subs/ai/_lib/ai.php, read the first time padAi runs.

  function padAi ( $prompt, $options = [] ) {

    include_once PAD . 'subs/ai/_lib/ai.php';

    return padAiAsk ( $prompt, $options, 'padAi' );

  }

  function padAiError () {

    return (string) padAiStore ( 'error' );

  }

  function padAiProviders () {

    return padKinds ( 'ai' );

  }

  function padAiStore ( $key, $value = NULL ) {

    static $store = [];

    if ( func_num_args () > 1 )
      $store [$key] = $value;

    return $store [$key] ?? NULL;

  }

?>
