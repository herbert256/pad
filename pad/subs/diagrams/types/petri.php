<?php
// petri - deterministic static, ordinary weighted place/transition nets.
include_once PAD.'subs/diagrams/_lib/petri.php';
$data=padDiagramPetriParse(padUnprotectQuotes(padDiagramSource()));
if(is_string($data)){if($padCheckSyntax)padError('the petri diagram: '.$data);return '';}
return padDiagramPetriSvg($data,(string)padTagParm('title','Petri net'));
