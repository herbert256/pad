<?php

  // The quick entry's day on 2026-10-17: today from this month, and from the newest month
  // with transactions when October has none yet - it was booked on September 30 - and an
  // older month's last day.

  $shown = implode ( ' ', [ ledgerQuickDay ( '2026-10', '2026-10', '2026-10-17' ),
                            ledgerQuickDay ( '2026-09', '2026-09', '2026-10-17' ),
                            ledgerQuickDay ( '2026-08', '2026-09', '2026-10-17' ) ] );

?>
