<?php
if(trim($padNotationText)==='')return '';
try{return padTruthHtml(padTruthRead($padNotationText),padNotationOption('title')?:'Truth table');}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
