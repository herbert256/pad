<?php

  // The palettes are the files of pad/subs/palettes/types/, as the engine reads them (padKinds).

  $tagAbout   = 'The colours of a palette as swatches with their hex values - ' . count ( padKinds ( 'palettes' ) ) . ' of them: viridis and its family, the colour-blind safe Okabe-Ito and Tol schemes, ColorBrewer\'s scales - the same palettes palette= gives a chart.';
  $tagGroup   = 'graphics';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{palette 'name'/}
{palette 'viridis', steps=7/}
{palette 'okabe-ito', title='Our chart colours'/}
{chart 'bar', data='sales', value='north, south', palette='viridis'/}
PAD;

  $tagParms   = [
    'name' => 'The palette, a file of <code>pad/subs/palettes/types/</code>: '
            . implode ( ', ', array_map ( fn ( $palette ) => "<code>$palette</code>", padKinds ( 'palettes' ) ) )
            . '. <code>padPalette ( \'viridis\', 7 )</code> answers the same colours to a page\'s PHP.' ];

  $tagOptions = [
    'steps' => 'How many colours, 1 to 256: a sequential or diverging palette sampled evenly from its first colour to its last, a categorical one its colours in order, starting again past its last. Without it a categorical palette shows its own, the others nine.',
    'title' => 'The name of the row for a screen reader, else the palette\'s name and the count.' ];

  $tagSee     = [ 'chart', 'theme', 'progress' ];

?>
