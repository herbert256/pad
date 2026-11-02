<?php

  // block - labelled nodes placed on an explicit grid, with row and column spans and routed connections.
  // Named links connect the blocks; TB, BT, LR and RL choose the layout direction without changing the source graph.
  //

include_once PAD.'subs/diagrams/_lib/block.php';
$data=padDiagramBlockParse(padUnprotectQuotes(padDiagramSource()));if(is_string($data)){if($padCheckSyntax)padError('the block diagram: '.$data);return '';}
return padDiagramBlockSvg($data,padDiagramDirection()?:'TB',(string)padTagParm('title','Block diagram'));
