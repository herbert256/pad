<?php

  // Staff paper on A4: rastral 3 - a staff 7.0 mm high, its five lines 1.75 mm apart, the size
  // of a part as Gould's table in Behind Bars gives it - and as many staves as fit with 2.2
  // staff heights between them, twelve, spread from the top margin of 15 mm to the bottom one,
  // 15 to 282 mm, over the width inside the margins, 15 to 195. rastral=0 is 9.2 mm and
  // rastral=8 3.7 mm; lines=6 is tablature, six lines a space apart, 8.75 mm high at rastral
  // 3; staves= says how many, a staff alone at the top.

  $answer = staffShow ( '{printable:staffpaper/}' )
          . ' // ' . staffShow ( '{printable:staffpaper rastral=0, staves=4/}' )
          . ' // ' . staffShow ( '{printable:staffpaper rastral=8, staves=2/}' )
          . ' // ' . staffShow ( "{printable:staffpaper lines=6, staves=1, paper='letter'/}" );

?>
