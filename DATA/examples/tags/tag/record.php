<?php

  $tagAbout   = 'Runs a SQL query on the application database and renders its content with the first row\'s columns as fields.';
  $tagGroup   = 'database';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:record "* from table where ..."} ... {/pad:record}
{pad:record "* from users where id=5", name='user'} ... @else@ ... {/pad:record}
PAD;

  $tagParms   = [
    'sql' => 'The query after the word SELECT - <code>"* from users where id=5"</code>.' ];

  $tagOptions = [
    'name' => 'In the designer preview (<code>&amp;padSample</code>) the sample\'s row of that name answers instead of the database; a capture records the database\'s answer under it.' ];

  $tagSee     = [ 'array', 'field', 'check' ];

?>
