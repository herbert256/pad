<?php
include_once PAD.'subs/diagrams/_lib/faulttree.php';
try{return padFaulttreeSvg(padFaulttreeRead(padUnprotectQuotes(padDiagramSource())),padParmText(padTagParm('title','faulttree')));}catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
