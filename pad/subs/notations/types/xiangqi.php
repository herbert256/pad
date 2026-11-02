<?php
if(trim($padNotationText)==='')return '';
$position=padXiangqiParse($padNotationText);if(is_string($position)){if($padCheckSyntax)padError('the xiangqi position: '.$position);return '';}
return padNotationWrap(padXiangqiSvg($position,padNotationChoice('direction','red',['red','black'])==='black',padNotationInt('size',420,120,2000),padNotationOption('title','Xiangqi position')));
