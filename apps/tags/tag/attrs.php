<?php

  $tagAbout   = 'Writes HTML attributes from values - quoted and escaped, a true boolean attribute bare, a false one left out.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{attrs name=$value, other=$value}
{attrs required, aria-expanded=$open}
{attrs $array}
PAD;

  $tagParms   = [
    'items' => 'Any number of items: <code>name=expression</code> (any HTML attribute name, dashes included), a bare <code>name</code> written as a bare attribute, or an expression whose array value adds its keys as attributes.' ];

  $tagOptions = [];

  $tagSee     = [ 'classes', 'input', 'echo' ];

?>
