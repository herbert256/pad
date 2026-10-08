<?php

  $tagAbout   = 'Asks the application database whether a row exists, and renders its content only when one does.';
  $tagGroup   = 'database';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{check "table where ..."} ... {/check}
{check "users where email='x@y.z'"} ... @else@ ... {/check}
PAD;

  $tagParms   = [
    'condition' => 'A table and a WHERE clause - <code>"users where active=1"</code>. No <code>* from</code>: CHECK takes the bare table.' ];

  $tagOptions = [
    'name' => 'In the designer preview (<code>&amp;padSample</code>) the sample\'s value of that name answers, true or false, instead of the database.' ];

  $tagSee     = [ 'record', 'field', 'array', 'if' ];

?>
