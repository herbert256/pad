# The OEIS table

`oeis.sqlite` holds the terms of the sequences of the On-Line Encyclopedia of Integer
Sequences that `{sequence:oeis 45}` reads, through `read.php`. This file records where they
came from and what the table holds, so a copy can be checked against it and a newer one built
the same way.

## Source

- The OEIS `stripped` file, <https://oeis.org/stripped.gz>: one line per sequence,
  `A000045 ,0,1,1,2,3,5,8,...,`, holding the terms each entry shows in its data section.
- Downloaded in August 2024. It went into the repository as the PHP constant `oeis.php` on
  2024-08-23 (a8c1d42512). Its highest A-number, A375613, is from that month.
- The OEIS data is published by The OEIS Foundation Inc. under the Creative Commons
  Attribution-ShareAlike 4.0 license (<https://oeis.org>, <https://creativecommons.org/licenses/by-sa/4.0/>).
  This table redistributes those terms; the changes made to them are listed below.

## History

| Date | Commit | What |
|------|--------|------|
| 2024-08-23 | a8c1d42512 | The download converted into the constant `oeis.php` |
| 2026-08-06 | 809ca05977 | The constant moved into `oeis.sqlite`, converted from the PHP table. That conversion left the row A375613, the last of the file, holding the PHP closing tag |
| 2026-10-21 | f462a24490 | Row A375613 given the 45 terms oeis.org answers for it (`search?q=id:A375613&fmt=json`) |

## Contents

As of 2026-10-21:

| | |
|-|-|
| Table | `oeis ( a INTEGER PRIMARY KEY, terms TEXT NOT NULL )` - the A-number without its leading zeroes, the terms as comma-separated text |
| Rows | 375,354 |
| A-numbers | A000001 to A375613. 259 between A368134 and A375612 have no row - entries not yet published when the file was downloaded - and read as an empty sequence |
| Terms | 71,876,751 bytes of text, every row whole numbers only (`-?[0-9]+(,-?[0-9]+)*`) |
| File | 78,356,480 bytes, sha256 `d5ce3946f7d9fb3d78d128d719580a596a45fd9f76be39c9609d5429eed75436` |

## Checking and rebuilding

- `shasum -a 256 oeis.sqlite` should give the sha256 above, and
  `sqlite3 oeis.sqlite 'SELECT COUNT(*), MIN(a), MAX(a) FROM oeis'` should give
  `375354|1|375613`.
- `regenerate.php` builds `test.sqlite` from a newer `stripped` file, expected at
  `dirname($padHome)/host/Downloads/stripped`. It reads the file a line at a time and leaves
  out any line whose terms are not all whole numbers. Check the result, rename it over
  `oeis.sqlite`, then update this file: the download date, the history, the counts, the size
  and the sha256. The Sequence suite will then report the pages that show OEIS terms. Explain
  every difference before re-recording their answers.
