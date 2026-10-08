<?php

  $tagAbout   = 'Writes the text a key has in the _lang/ catalog of the request\'s locale, with plural forms and substitutions.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{trans 'key'}
{trans 'key', count=$n}
{trans 'key', name=$value, other=$value}
PAD;

  $tagParms   = [
    'key' => 'The key in <code>_lang/&lt;locale&gt;.json</code>. A key no catalog knows is written as itself.' ];

  $tagOptions = [
    'count' => 'Picks the plural form of a text with <code>|</code> between its forms, and replaces <code>%d</code> in it.',
    'name'  => 'Any other name: its value replaces <code>:name</code> in the text. The names are read raw, so even <code>content=</code> or <code>data=</code> is a substitution here.' ];

  $tagSee     = [ 'echo', 'markdown' ];

?>
