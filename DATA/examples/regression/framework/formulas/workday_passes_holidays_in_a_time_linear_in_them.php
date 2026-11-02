<?php

  // WORKDAY and WORKDAY.INTL look at each holiday once (_lib/dates.php padFormulaWorkday):
  // holidays on the workdays right after 2024-01-15 - each round of the walk passing as many
  // holidays as it stepped - take a time linear in them. Every holiday was compared after
  // every round, a time quadratic in them: 4,000 took 187 ms, the 20,000 timed here, on the
  // function itself, some five seconds each way. The answers, of 2,000 such holidays as cells,
  // are the first workday after them, and before them going back.

  padFormulaLoad ();

  $workWeekend = padFormulaWeekend ( NULL, [] );
  $workAfter   = $workBefore = [];

  for ( $day = 45306; count ( $workAfter ) < 20000; )
    if ( ! $workWeekend [ padFormulaWeekday ( ++$day ) ] )
      $workAfter [] = $day;

  for ( $day = 45306; count ( $workBefore ) < 20000; )
    if ( ! $workWeekend [ padFormulaWeekday ( --$day ) ] )
      $workBefore [] = $day;

  $workBefore = array_reverse ( $workBefore );

  $workCells = fn ( $days ) => array_map ( fn ( $day ) => [ (float) $day ], $days );

  $workOut = [
    padFormula ( '=WORKDAY(45306,1,A1:A2000)', $workCells ( array_slice ( $workAfter, 0, 2000 ) ) ),
    padFormula ( '=WORKDAY.INTL(45306,-1,1,A1:A2000)', $workCells ( array_slice ( $workBefore, -2000 ) ) ),
    padFormula ( '=NETWORKDAYS(45306,45306+2800,A1:A2000)', $workCells ( array_slice ( $workAfter, 0, 2000 ) ) )
  ];

  $workStart = hrtime ( TRUE );

  $workOut [] = padFormulaWorkday ( 45306,  1, $workWeekend, $workAfter );
  $workOut [] = padFormulaWorkday ( 45306, -1, $workWeekend, $workBefore );

  $cases = implode ( ' ', $workOut ) . ' ' . ( hrtime ( TRUE ) - $workStart < 1e9 ? 'in time' : 'too slow' );

?>
