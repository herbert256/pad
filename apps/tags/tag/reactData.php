<?php

  $tagAbout   = 'Writes the mount point of a React component, its data from a provider in _providers/ as JSON in an attribute.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{reactData id='products'}
{reactData id='products', provider='catalog', type='array'}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'id'       => 'The id of the <code>&lt;div&gt;</code>, and the key the result is kept under in <code>$padProviders</code> - default <code>myReactId</code>.',
    'provider' => 'The file <code>_providers/&lt;name&gt;.php</code> that answers the data - default the id. A plain name only.',
    'type'     => '<code>check</code> turns the result into 1 or 0; otherwise (<code>record</code>, the default, or <code>array</code>) it is passed as the provider returned it.' ];

  $tagSee     = [ 'vite', 'echo' ];

?>
