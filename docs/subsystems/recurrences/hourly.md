# recurrences: hourly

start requires a whole-second instant/time. step=elapsed (default) advances interval×3600 seconds and includes both DST-fold instants; step=wall advances nominal local hours, skips gaps and chooses the first fold instant (an explicit offset start keeps its supplied instant). count counts valid generated occurrences before exclusions; until inclusive, mutually exclusive with count. Range is [from,to); date exclusions remove a local day, datetime exclusions one instant. Output cap 1000 default, ≤10000; scan cap 200000 default, ≤1000000. Caps throw without partial output. Job execution is separate.

Runnable example: `apps/tags/examples/recurrence/hourly.pad`. Independent fixture: `php tests/subsystems/expansion-recurrences-hourly.php`.
