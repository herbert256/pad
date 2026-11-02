# Micro QR

`{barcode:microqr '12345'/}` produces M1 (11×11 modules plus a two-module quiet zone). `version='M1'|'M2'|'M3'|'M4'|'auto'` constrains dimensions; automatic picks the smallest that fits. `level='auto'` uses M1's error detection only, or L for larger versions. Explicit `level='none'` is M1 only, L/M are M2–M4, and Q is M4 only; H is unsupported. Unlike ordinary QR, M1 does not provide error correction. M2/M3/M4 are 13/15/17 modules wide. Default SVG size is 128 pixels.

Modes: `auto`, `numeric`, `alnum`, `byte`, `kanji`. M1 permits numeric, M2 numeric/alphanumeric, M3/M4 all four. This encoder uses one segment per symbol; it does not optimize mixed-mode segmentation. Alphanumeric is the QR 45-character alphabet. Kanji uses the QR Shift-JIS ranges. Byte mode defaults to UTF-8; optional `encoding='ISO-8859-1'|'SJIS'|'binary'` controls its bytes. Micro QR has no ECI, so receiving applications must agree on the byte encoding; binary passes the helper input unchanged. UTF-8 input/transcoding is validated. `mask=-1` chooses the best of four Micro QR masks; 0–3 explicitly selects one. All invalid mode/version/level combinations and over-capacity payloads fail instead of falling back to QR.

`padMicroQrEncode($text,$options)` returns matrix, version, level, mode, mask, or a descriptive error string. The template adapter uses PAD's existing SVG writer with accessible escaped title and color/background options. Geometry, mode/count fields, shortened final codewords, Reed-Solomon block size, masking/scoring and format information are native Micro QR implementations. Only payload bit helpers and GF(256) polynomial arithmetic are shared with ordinary QR.

| Version/level | Digits | Alphanumeric | Bytes | Kanji |
|---|---:|---:|---:|---:|
| M1/none | 5 | — | — | — |
| M2/L | 10 | 6 | — | — |
| M2/M | 8 | 5 | — | — |
| M3/L | 23 | 14 | 9 | 6 |
| M3/M | 18 | 11 | 7 | 4 |
| M4/L | 35 | 21 | 15 | 9 |
| M4/M | 30 | 18 | 13 | 8 |
| M4/Q | 21 | 13 | 9 | 5 |

Validation: `php tests/subsystems/code-microqr.php` checks 250 exact Zint matrices, all mode/version/ECC combinations, every forced mask and automatic mask, both short and maximum-capacity payloads, overflow rejection and SVG escaping. `python tests/subsystems/code-microqr-oracle.py` (numpy + zxing-cpp) adds 500 seeded varied payloads and decodes all 750 at three module sizes: 2250 successful independent ZXing-C++ decodes. Physical printed-label scanning has not been performed; keep the quiet zone and choose a module size appropriate for the printer/scanner.

References: ISO/IEC 18004:2015 sections 7.4, 7.6–7.9; Zint `9dd9791c43d6cde69d27e46f1c39874011aca4c3` [qr.c](https://github.com/zint/zint/blob/9dd9791c43d6cde69d27e46f1c39874011aca4c3/backend/qr.c); Segno `b11dc2913c22b22b3bc0a6efaa989904c44977bf` [encoder.py](https://github.com/heuer/segno/blob/b11dc2913c22b22b3bc0a6efaa989904c44977bf/segno/encoder.py). Compatible BSD notices are in MICROQR-LICENSE. Capacity and exact padding fixtures follow Zint; Segno was also consulted for layout/mask descriptions. No third-party runtime, process execution or network access is required by PAD's encoder.
