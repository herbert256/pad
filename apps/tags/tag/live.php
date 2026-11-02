<?php

  $tagAbout   = 'A region of the page that re-renders on the server when something in it is clicked, submitted or changed, and swaps itself in.';
  $tagGroup   = 'pages';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:live 'name'} ... <button pad-click="event" pad-value="value">...</button> ... {/pad:live}
{pad:live 'name'} ... <form pad-submit="event">...</form> ... {/pad:live}
{pad:live 'name'} ... <select name="field" pad-change="event">...</select> ... {/pad:live}
PAD;

  $tagParms   = [
    'name' => 'The region\'s name - letters, digits, <code>_</code> and <code>-</code>. Give each region of a page its own name: an event is answered by the first region of that name.' ];

  $tagOptions = [];

  $tagSee     = [ 'ajax', 'fragment', 'form', 'page' ];

?>
