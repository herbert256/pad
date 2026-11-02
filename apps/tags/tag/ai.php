<?php

  // The providers are the files of pad/subs/ai/types/, as the engine reads them (padAiProviders).

  $tagAbout   = 'Prints the answer of a language model to the prompt its content renders, as text, kept for a while.';
  $tagGroup   = 'network';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:ai provider='anthropic', model='name', system='text', tokens=500, ttl=86400} ... {/pad:ai}
{pad:ai 'prompt'/}
PAD;

  $tagParms   = [
    'prompt' => 'The prompt of the single form. As a pair the rendered content is the prompt.' ];

  $tagOptions = [
    'provider' => 'A file of <code>pad/subs/ai/types/</code>: ' . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padAiProviders () ) )
                  . ' - <code>PAD_AI_PROVIDER</code>, else <code>anthropic</code>.',
    'model'    => 'The model - the provider\'s own default unless given.',
    'system'   => 'The system prompt.',
    'tokens'   => 'The most the answer may take.',
    'ttl'      => 'The seconds an answer is kept - 3600 unless given, 0 keeps nothing.' ];

  $tagSee     = [ 'curl', 'cache', 'markdown' ];

  $subsystemKindLinks = appSubsystemLinks ( 'ai' );

?>
