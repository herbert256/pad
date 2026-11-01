<?php

  // The themes the switcher offers are the files of pad/subs/themes/types/ (padKinds).

  $tagAbout   = 'A switcher of the themes: the visitor\'s choice kept in a cookie of the application, its pages in that theme from then on - a form, no JavaScript needed.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{themes/}
{themes label='Colours', button='Use'/}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'label'  => 'The label of the list of themes (<code>Theme</code>).',
    'button' => 'The text of the button that applies the choice (<code>Apply</code>).' ];

  $tagSee     = [ 'theme', 'form' ];

?>
