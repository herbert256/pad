<?php

  $tagAbout   = 'Runs a SQL query on the application database and repeats its content once per row.';
  $tagGroup   = 'database';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{array "* from table where ..."} ... {/array}
{array "* from orders", name='orders'} ... {/array}
PAD;

  $tagParms   = [
    'sql' => 'The query after the word SELECT - <code>"* from users order by name"</code>.' ];

  $tagOptions = [
    'name' => 'Names the level, so its properties read <code>first@orders</code>; in the designer preview (<code>&amp;padSample</code>) the sample\'s rows of that name answer instead of the database.' ];

  $tagSee     = [ 'record', 'field', 'check', 'pager' ];

?>
