# Formula: ISOWEEKNUM

`ISOWEEKNUM(serial)` returns the ISO Monday-based week with week 1 containing January 4. Uses the shared 1900 serial helpers and their fictitious leap-day convention, not the host timezone. Fractional days are truncated; invalid dates propagate existing #NUM!/#VALUE! errors. Example: `=ISOWEEKNUM(DATE(2021,1,1))` gives 53. Gregorian oracle covers 1461 dates in 2020–2023.

Reference contract and examples: https://support.microsoft.com/en-us/excel/functions/isoweeknum-function . Run `php tests/subsystems/formula-isoweeknum.php`.
