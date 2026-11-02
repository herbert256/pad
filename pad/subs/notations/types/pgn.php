<?php
// pgn - bounded annotated mainline and optional initial FEN board; no legal-move execution.
$game=padPgnParse($padNotationText);if(is_string($game)){if($padCheckSyntax)padError('the PGN record: '.$game);return '';}
return padNotationWrap(padNotationStyle('pgn',[]).padPgnHtml($game,(bool)padTagParm('board',FALSE),(bool)padTagParm('flip',FALSE),padNotationInt('size',360,120,1000)));
