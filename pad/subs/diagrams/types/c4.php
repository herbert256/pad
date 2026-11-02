<?php
include_once PAD.'subs/diagrams/_lib/c4.php';
$data=padDiagramC4Parse(padUnprotectQuotes(padDiagramSource()));if(is_string($data)){if($padCheckSyntax)padError('the c4 diagram: '.$data);return '';}
return padDiagramC4Svg($data,padDiagramDirection()?:'TB',(string)padTagParm('title','C4 architecture'));
