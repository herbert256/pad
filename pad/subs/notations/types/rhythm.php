<?php
if(trim($padNotationText)==='')return '';
try{$sub=padTagParm('subdivision',4);if(is_string($sub)&&ctype_digit($sub))$sub=(int)$sub;return padRhythmHtml(padRhythmRead($padNotationText,padNotationOption('meter')?:'4/4',$sub),padNotationOption('title')?:'Rhythm');}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
