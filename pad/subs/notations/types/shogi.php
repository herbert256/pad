<?php
// shogi - static SFEN with promotions, hands and two board orientations.
$position=padShogiParse($padNotationText);if(is_string($position)){if($padCheckSyntax)padError('the shogi position: '.$position);return '';}
return padNotationWrap(padShogiSvg($position,padNotationChoice('direction','sente',['sente','gote'])==='gote',padNotationInt('size',460,120,1200),padNotationOption('title','Shogi position')));
