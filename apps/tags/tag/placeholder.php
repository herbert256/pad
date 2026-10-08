<?php

  $tagAbout   = 'Draws a grey image placeholder as inline SVG - crossing lines and the size or a text in the middle.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{placeholder '600x300', text='text'}
{placeholder ratio='16:9', width=640, fluid}
PAD;

  $tagParms   = [
    'size' => 'The size, width x height: <code>\'600x300\'</code>. Left out when <code>ratio</code> is given.' ];

  $tagOptions = [
    'ratio' => 'Proportions instead of a size (<code>\'16:9\'</code>), drawn <code>width</code> pixels wide.',
    'width' => 'The width of a box drawn from <code>ratio</code>, default 640.',
    'text'  => 'Written in the middle and the accessible name; by default the size (<code>600 × 300</code>) or the ratio.',
    'fluid' => 'Bare option: as wide as the container (<code>width="100%"</code>), the height following from the proportions.' ];

  $tagSee     = [ 'img', 'lorem' ];

?>
