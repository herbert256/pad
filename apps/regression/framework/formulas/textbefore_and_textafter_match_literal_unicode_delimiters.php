<?php
  $cases = [
    '=TEXTBEFORE("Ada Lovelace"," ")' => 'Ada',
    '=TEXTAFTER("Ada Lovelace"," ")' => 'Lovelace',
    '=TEXTBEFORE("a/b/c","/",-1)' => 'a/b',
    '=TEXTAFTER("a/b/c","/",-2)' => 'b/c',
    '=TEXTBEFORE("α🌍β🌍γ","🌍",2)' => 'α🌍β',
    '=TEXTAFTER("caféÉfin","é",2,1)' => 'fin',
    '=TEXTBEFORE("aaaa","aaa",-1)' => 'a',
    '=TEXTAFTER("aaaa","aaa",1)' => 'a',
    '=TEXTBEFORE("a*b?c","*")' => 'a',
    '=TEXTAFTER("a*b?c","?")' => 'c',
    '=TEXTBEFORE("abc","",-1)' => 'abc',
    '=TEXTAFTER("abc","",-1)' => '',
    '=TEXTBEFORE("abc","")' => '',
    '=TEXTAFTER("abc","")' => 'abc',
    '=TEXTBEFORE("Socrates"," ",,,1)' => 'Socrates',
    '=TEXTAFTER("Socrates"," ",,,1)' => '',
    '=TEXTBEFORE("a/b","/",-2,0,1)' => '',
    '=TEXTAFTER("a/b","/",-2,0,1)' => 'a/b',
    '=TEXTBEFORE("abc","!",1,0,0,"missing")' => 'missing',
    '=TEXTAFTER("abc","!",1,0,0,42)' => 42
  ];
  $failures = [];
  foreach ( $cases as $formula => $expected ) if ( padFormula ( $formula ) !== $expected ) $failures [] = $formula;
  $answer = $failures ? implode ( '; ', $failures ) : count ( $cases ) . ' delimiter extractions match';
?>
