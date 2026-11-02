<?php

  // A count, a place or a number of digits a function takes as a whole number (_lib/
  // formulas.php) counts as 2^53 at most - past it no float holds every whole number - and
  // each function answers what it answers for a count past its end: LEFT, MID and RIGHT the
  // whole text, ROUND the number, CHOOSE and SEARCH #VALUE!, INDEX and VLOOKUP #REF!, DATE,
  // WEEKDAY, LARGE, SMALL, EDATE and EOMONTH #NUM! - where 1E300 was cast to an int that cannot
  // hold it and the request ended: =LEFT("a",1E300) typed into any editable cell was a 500.

  $cases = formulasRun ( [
    '=LEFT("abc",1E300)',
    '=MID("abcdef",2,1E20)',
    '=RIGHT("abc",1E300)',
    '=CHOOSE(1E19,1,2)',
    '=ROUND(1.5,1E30)',
    '=ROUNDUP(1.5,-1E30)',
    '=DATE(2024,1E300,1)',
    '=DATE(2024,-1E300,1)',
    '=WEEKDAY(1,1E300)',
    '=SEARCH("a","abc",1E300)',
    '=INDEX(B2:B7,1E300)',
    '=VLOOKUP(1,A1:B7,1E300,FALSE)',
    '=LARGE(B2:B7,1E300)',
    '=SMALL(B2:B7,-1E300)',
    '=EDATE(1,9.2E18)',
    '=EOMONTH(1,-1E300)',
    '=SUBSTITUTE("a-b","-","+",1E300)',
    '=XLOOKUP(1,B2:B7,C2:C7,,1E300)'
  ] );

?>
