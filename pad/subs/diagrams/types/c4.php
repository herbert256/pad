<?php

  // c4 - software architecture with people, systems, containers and components inside optional nested boundaries.
  // Labelled relationships connect explicit node identifiers; the supported bounded grammar is documented with examples.
  //

include_once PAD.'subs/diagrams/_lib/c4.php';
$data=padDiagramC4Parse(padUnprotectQuotes(padDiagramSource()));if(is_string($data)){if($padCheckSyntax)padError('the c4 diagram: '.$data);return '';}
return padDiagramC4Svg($data,padDiagramDirection()?:'TB',(string)padTagParm('title','C4 architecture'));
