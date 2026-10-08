<?php

  $tagAbout   = 'A form that posts back to the page, carrying the session\'s CSRF token and its own name, its fields refilling when it came back.';
  $tagGroup   = 'forms';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{form 'name'} ... {/form}
{form 'name', method='get', class='search'} ... {/form}
{form 'name', error='Please correct the errors below.'} ... {/form}
{form 'name', client, error} ... {/form}
PAD;

  $tagParms   = [
    'name' => 'The form\'s name, sent back in a hidden <code>padForm</code> field: <code>padPosted(\'name\')</code> is TRUE when this form came back. Optional for a form without rules.' ];

  $tagOptions = [
    'method' => '<code>post</code> (the default) or <code>get</code>. A GET form gets no token and no name field, and its fields refill from the query string.',
    'error'  => 'The message shown above the fields, in <code>&lt;div class="error" role="alert"&gt;</code>, when the form came back with errors. Bare option: <em>Please correct the errors below.</em>',
    'client' => 'Bare option: check the <code>rules=</code> of the fields in the browser too, with the same messages - the form gets <code>novalidate</code>, <code>data-pad-rules</code> and the checker script.' ];

  $tagSee     = [ 'input', 'textarea', 'csrf', 'validator', 'flash', 'attrs' ];

?>
