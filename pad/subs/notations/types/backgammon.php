<?php

  // backgammon - a board position with numbered points, checkers, bar, borne-off pieces and doubling cube.
  // Use start or the documented point assignments; each player must account for fifteen checkers.
  // direction='black' turns the board around. This displays a position without playing moves.
  //
  //   {notation:backgammon}start{/notation:backgammon}

if(trim($padNotationText)==='')return '';
$position=padBackgammonParse($padNotationText);if(is_string($position)){if($padCheckSyntax)padError('the backgammon position: '.$position);return '';}
return padNotationWrap(padBackgammonSvg($position,padNotationChoice('direction','white',['white','black'])==='black',padNotationInt('size',640,160,2000),padNotationOption('title','Backgammon position')));
