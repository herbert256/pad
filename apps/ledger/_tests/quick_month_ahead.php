<?php

  // The quick entry's day on 2026-10-17 from a month ahead of today - November, the newest
  // since its rent was booked ahead, and December - is today, where it was that month's
  // last day: the coffee of today booked on November 30 and counted in November's budgets.

  $shown = implode ( ' ', [ ledgerQuickDay ( '2026-11', '2026-11', '2026-10-17' ),
                            ledgerQuickDay ( '2026-11', '2026-12', '2026-10-17' ),
                            ledgerQuickDay ( '2026-09', '2026-11', '2026-10-17' ) ] );

?>
