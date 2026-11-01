<?php

  // {ai provider='anthropic', ttl=86400}Summarise in one sentence: {$body}{/ai} - the answer of a
  // language model, printed as text (lib/ai.php, subs/ai/): the content between the tags is
  // rendered first - like {live} and {toast}, the pair asks for the end walk - and what it
  // rendered is the prompt, its fields read as the text they hold. The single form takes the
  // prompt as its parameter: {ai 'Name three rivers of France.'/}.
  //
  // The answer is a value, never template code: escaped for HTML and its braces inert, so an
  // answer of {php:getcwd} prints those characters. provider= is a file of subs/ai/types/ -
  // anthropic, bedrock, cloudflare, cohere, gemini, lmstudio, mistral, ollama, openai; model=
  // its model, system= the system prompt, tokens= the most the answer may take (the handling
  // option max is something else), ttl= the seconds the answer is kept - 3600 unless given, 0
  // keeps nothing.
  //
  // A missing key, an unknown provider, a wrong option or an error of the API is an error
  // under the strict check; without it the tag answers nothing and the error is logged.

  include_once PAD . 'subs/ai/_lib/ai.php';

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padAiOptions = [];

  foreach ( [ 'provider', 'model', 'system', 'tokens', 'ttl' ] as $padAiName )
    if ( ( $padAiValue = padTagParm ( $padAiName, NULL ) ) !== NULL )
      $padAiOptions [$padAiName] = is_string ( $padAiValue ) ? padParmText ( $padAiValue ) : $padAiValue;

  $padAiPrompt = trim ( padParmText ( $padPair [$pad] ? (string) $padContent : ( is_scalar ( $padParm ) ? (string) $padParm : '' ) ) );

  $padAiKind = strtolower ( trim ( (string) ( $padAiOptions ['provider'] ?? padEnv ( 'PAD_AI_PROVIDER', 'anthropic' ) ) ) );

  if ( ( $padInfoXref ?? FALSE ) and padKind ( 'ai', $padAiKind ) )
    padInfoXref ( 'ai', $padAiKind );

  $padAiAnswer = padProtect ( htmlspecialchars ( padAiAsk ( $padAiPrompt, $padAiOptions, '{ai}' ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) );

  if ( ! $padPair [$pad] )
    return $padAiAnswer;

  $padContent = $padAiAnswer;

  return TRUE;

?>
