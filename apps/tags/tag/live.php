<?php

  $tagAbout   = 'A region of the page that re-renders on the server when something in it is clicked, submitted or changed, and swaps itself in.';
  $tagGroup   = 'pages';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{live 'name'} ... <button pad-click="event" pad-value="value">...</button> ... {/live}
{live 'name'} ... <form pad-submit="event">...</form> ... {/live}
{live 'name'} ... <select name="field" pad-change="event">...</select> ... {/live}
PAD;

  $tagParms   = [
    'name' => 'The region\'s name - letters, digits, <code>_</code> and <code>-</code>. Give each region of a page its own name: an event is answered by the first region of that name.' ];

  $tagOptions = [];

  $tagSee     = [ 'ajax', 'fragment', 'form', 'page' ];

?>
