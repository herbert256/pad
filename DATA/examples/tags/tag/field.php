<?php

  $tagAbout   = 'Runs a SQL query on the application database and prints the single value it answers.';
  $tagGroup   = 'database';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{field "column from table where ..."}
{field "count(*) from orders", name='orderCount'}
PAD;

  $tagParms   = [
    'sql' => 'The query after the word SELECT - <code>"count(*) from users"</code>. The tag name is the <code>db()</code> command word, so the parameter never starts with SELECT.' ];

  $tagOptions = [
    'name' => 'In the designer preview (<code>&amp;padSample</code>) the value of that name in the page\'s sample answers instead of the database; <code>&amp;padSample=capture</code> records the database\'s answer under it.' ];

  $tagSee     = [ 'record', 'array', 'check' ];

?>
