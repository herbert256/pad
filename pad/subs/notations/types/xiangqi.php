<?php

  // xiangqi - a Chinese chess position on its nine-file, ten-rank board, with river and palaces.
  // Use start or a FEN piece-placement field; direction='black' turns the board around.
  // Positions are checked for material and palace bounds; moves are not played or validated.
  //
  //   {notation:xiangqi}start{/notation:xiangqi}

if(trim($padNotationText)==='')return '';
$position=padXiangqiParse($padNotationText);if(is_string($position)){if($padCheckSyntax)padError('the xiangqi position: '.$position);return '';}
return padNotationWrap(padXiangqiSvg($position,padNotationChoice('direction','red',['red','black'])==='black',padNotationInt('size',420,120,2000),padNotationOption('title','Xiangqi position')));
