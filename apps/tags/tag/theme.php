<?php

  // The themes are the files of pad/subs/themes/types/, as the engine reads them (padKinds).

  $tagAbout   = 'The colours of the page swapped for a theme - ' . count ( padKinds ( 'themes' ) ) . ' of them, from paper and sepia to a terminal, an ocean and a high-contrast one - or its content alone in a box of the theme.';
  $tagGroup   = 'layout';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{theme 'name'/}
{theme 'paper'/}
{theme 'terminal'} ... {/theme}
$padTheme = 'paper';
PAD;

  $tagParms   = [
    'name' => 'The theme, a file of <code>pad/subs/themes/types/</code>: '
            . implode ( ', ', array_map ( fn ( $theme ) => "<code>$theme</code>", padKinds ( 'themes' ) ) ) . '.' ];

  $tagOptions = [];

  $tagSee     = [ 'themes', 'palette', 'section' ];

?>
