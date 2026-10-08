<?php

  $tagAbout   = 'Draws a flowchart or a sequence diagram from Mermaid-style lines of text, laid out on the server as inline SVG.';
  $tagGroup   = 'graphics';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{diagram direction='LR', edges='curved', title='text'} A[Start] --> B{Ready?} {/diagram}
{diagram type='sequence', title='text'} Shop ->> Bank: Charge {/diagram}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'type'      => '<code>flowchart</code> (default) or <code>sequence</code> - a first line <code>sequenceDiagram</code> says so too.',
    'direction' => '<code>TB</code> (default), <code>BT</code>, <code>LR</code> or <code>RL</code> - else a first line <code>graph LR</code> or <code>flowchart LR</code> names it.',
    'edges'     => 'How a flowchart\'s edges run: <code>orthogonal</code> (default, rounded turns), <code>straight</code> or <code>curved</code>.',
    'title'     => 'The accessible name, default <code>Flowchart</code> or <code>Sequence diagram</code>.' ];

  $tagSee     = [ 'chart', 'timeline' ];

?>
