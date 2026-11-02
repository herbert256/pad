<?php
include_once PAD.'subs/diagrams/_lib/bpmn.php';
try{return padBpmnSvg(padBpmnRead(padUnprotectQuotes(padDiagramSource())),padParmText(padTagParm('title','bpmn')));}catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
