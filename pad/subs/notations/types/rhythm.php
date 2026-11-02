<?php

  // rhythm - A labelled, accessible one-measure beat grid. `meter="4/4"` (1..12 beats; denominator 2, 4, 8 or 16), `subdivision=4` (1..8 per beat). Each line is `Instrument: X... x... X... x...`; X is an accent, x a hit, . a rest; spaces and bars separate beats. Every row must contain exactly beats × subdivisions cells.
  //
  // At most 16 instruments and 8,000 input bytes. Headers number beats and subdivisions. It renders a semantic table without playback; screen readers hear hit/accent/rest.

if(trim($padNotationText)==='')return '';
try{$sub=padTagParm('subdivision',4);if(is_string($sub)&&ctype_digit($sub))$sub=(int)$sub;return padRhythmHtml(padRhythmRead($padNotationText,padNotationOption('meter')?:'4/4',$sub),padNotationOption('title')?:'Rhythm');}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
