<?php

  // April 2026 in the Netherlands has 22 weekdays; Easter Monday (the 6th) and King's Day
  // (the 27th) are days off, Good Friday (the 3rd) is not: 20 workdays, -20 backwards. One
  // workday after Friday 24 April is Tuesday the 28th. In Bavaria 2026 has 261 weekdays and
  // nine public holidays on them. Three workdays after 23 December 2026 in England is the
  // 30th: Christmas on Friday, Boxing Day's substitute on Monday the 28th.

  $work = implode ( ' ', [
    padIsHoliday ( '2026-04-27', 'nl' ) ? 'yes' : 'no',
    padIsHoliday ( '2026-04-03', 'nl' ) ? 'yes' : 'no',
    padIsHoliday ( '2026-01-06', 'de-by' ) ? 'yes' : 'no',
    padIsHoliday ( '2026-01-06', 'de' ) ? 'yes' : 'no',
    padWorkdays ( '2026-04-01', '2026-04-30', 'nl' ),
    padWorkdays ( '2026-04-30', '2026-04-01', 'nl' ),
    padWorkdays ( '2026-01-01', '2026-12-31', 'de-by' ),
    padWorkdays ( '2026-04-25', '2026-04-26', 'nl' ),
    padAddWorkdays ( '2026-04-24', 1, 'nl' ),
    padAddWorkdays ( '2026-04-28', -1, 'nl' ),
    padAddWorkdays ( '2026-04-25', 0, 'nl' ),
    padAddWorkdays ( '2026-12-23', 3, 'gb' ),
    padAddWorkdays ( '2026-12-30', 2, 'nl' )
  ] );

?>
