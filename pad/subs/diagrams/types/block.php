<?php
include_once PAD.'subs/diagrams/_lib/block.php';
$data=padDiagramBlockParse(padUnprotectQuotes(padDiagramSource()));if(is_string($data)){if($padCheckSyntax)padError('the block diagram: '.$data);return '';}
return padDiagramBlockSvg($data,padDiagramDirection()?:'TB',(string)padTagParm('title','Block diagram'));
