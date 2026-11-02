<?php

  // The themes the switcher offers are the files of pad/subs/themes/types/ (padKinds).

  $tagAbout   = 'A switcher of the themes: the visitor\'s choice kept in a cookie, the pages built on the frame in that theme from then on, applied the moment it is chosen.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{widget:themes/}
{widget:themes label='Colours', button='Use'/}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'label'  => 'The label of the list of themes (<code>Theme</code>), for a screen reader - it is not shown.',
    'button' => 'The text of the button that applies the choice without JavaScript (<code>Apply</code>).' ];

  $tagSee     = [ 'theme', 'form' ];

  $subsystemKindLinks = appSubsystemLinks ( 'theme' );

?>
