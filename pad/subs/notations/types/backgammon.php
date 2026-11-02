<?php
if(trim($padNotationText)==='')return '';
$position=padBackgammonParse($padNotationText);if(is_string($position)){if($padCheckSyntax)padError('the backgammon position: '.$position);return '';}
return padNotationWrap(padBackgammonSvg($position,padNotationChoice('direction','white',['white','black'])==='black',padNotationInt('size',640,160,2000),padNotationOption('title','Backgammon position')));
