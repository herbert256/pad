# Code 11

`{barcode:code11 '123-45', checks=2/}` renders a native SVG. Only 1–140 ASCII digits/hyphens are accepted; no whitespace trimming. `checks=2` (default) appends C and K, `checks=1` appends C only, `checks=0` none. This is an explicit policy, independent of payload length. C weights cycle 1–10 from the right; K includes C and cycles 1–9; remainder 10 is a hyphen. The readable label includes generated checks. Quiet zones are ten narrow modules. Existing height/scale/title/plain/color/background options apply.

`padCode11($text,$checks)` returns the normal linear-barcode tuple or an error string. Pattern widths and check arithmetic were cross-checked against Zint commit `9dd9791c43d6cde69d27e46f1c39874011aca4c3`, backend/code11.c. Its BSD notice is in CODE11-LICENSE. Six exact symbol fixtures come from Zint's independently TEC-IT-verified test_code11.c, including both check characters being hyphens. Run `php tests/subsystems/code-code11.php`.

References: https://zint.org.uk/manual/chapter/6/ and https://github.com/zint/zint/blob/9dd9791c43d6cde69d27e46f1c39874011aca4c3/backend/tests/test_code11.c . Actual printed-label scanning depends on printer/module size and scanner Code 11/checksum configuration; physical scanning has not been performed here.
