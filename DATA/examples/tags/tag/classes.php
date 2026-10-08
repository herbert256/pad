<?php

  $tagAbout   = 'Builds a class list from fixed names and names that are in only while their condition holds.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{classes 'fixed names', name=condition, other=condition}
{classes $array}
PAD;

  $tagParms   = [
    'items' => 'Any number of items: an expression - a text of one or more names, or an array of them - always in; <code>name=condition</code> puts that name in when the condition holds.' ];

  $tagOptions = [];

  $tagSee     = [ 'attrs', 'switch', 'if' ];

?>
