<?php
if(trim($padNotationText)==='')return '';
$spec=padFretboardParse($padNotationText,padNotationOption('tuning','E2 A2 D3 G3 B3 E4'));
if(is_string($spec)){if($padCheckSyntax)padError('the fretboard '.$spec);return '';}
return padNotationWrap(padFretboardSvg($spec,padNotationInt('firstfret',0,0,24),padNotationInt('frets',12,1,24),padNotationChoice('labels','notes',['notes','intervals']),padNotationChoice('direction','right',['right','left'])==='left',padNotationInt('size',720,120,2000),padNotationOption('title',$padNotationText)));
