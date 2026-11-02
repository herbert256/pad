# rMQR and MaxiCode

These native PHP encoders use bounded, explicitly supported encodings. They need no
runtime service, executable or image library. The machine-readable results are
checked against Zint and decoded independently by ZXing; they are not decorative
approximations. Helpers return a result array or a descriptive error string;
template kinds pass failures through the existing `padCodeFail` contract.

## rMQR

`{barcode:rmqr 'PAD-2026', version='R11x43', level='M', size=320/}`

`padRmqrEncode(text, options)` supports every one of the 32 ISO/IEC 23941:2022 sizes:
R7x43/59/77/99/139, R9x43/59/77/99/139, R11x27/43/59/77/99/139,
R13x27/43/59/77/99/139, R15x43/59/77/99/139, R17x43/59/77/99/139.
`version='auto'` selects the smallest area that fits, with registry order breaking
ties. `level='M'` is the default; H is also supported. L and Q are rejected.

`mode='auto'` chooses one numeric, alphanumeric or byte segment. Explicit
`numeric`, `alnum`, `byte` select a single segment. Alphanumeric uses the QR alphabet
`0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:`. There is no mixed-segment optimizer,
Kanji mode, GS1 or structured append. Unsupported option names/modes are rejected.
This can reject text an optimizing encoder could fit in a selected small size.

`encoding='ISO-8859-1'` (default) treats supplied text as UTF-8 and converts it
losslessly to Latin-1 for byte segments; unrepresentable text fails.
`encoding='UTF-8'` emits ECI 26 with byte segments. `encoding='binary'` preserves
raw bytes without ECI. No input is silently transcoded with replacements. Input is
nonempty and at most 4096 bytes; actual symbol capacity is lower and checked.

Result: `matrix`, `version`, `level`, `mode`, `encoding`, `codewords`. Template
rendering preserves rectangular module aspect ratio and a two-module quiet zone;
`size` is width in pixels. `title`, `color`, `background` follow other barcode tags.

## MaxiCode

`{barcode:maxicode 'TRACKING 123456789', mode=4, size=240/}`

`{barcode:maxicode 'PARCEL 123456789', mode=2, postcode='12345', country=840, service=1/}`

`padMaxicodeEncode(text, options)` supports mode 2 (numeric structured postcode),
mode 3 (alphanumeric structured postcode), mode 4 (unstructured standard error
correction, default), and mode 5 (unstructured enhanced error correction).
Mode 6, structured append and automatic carrier-message headers are not supported.
All unknown options are rejected. Mode 2/3 requires explicit `postcode` string and
integer `country`/`service` values in 0..999. Mode 2 accepts 1..9 postcode digits;
for country 840, five digits are expanded with four trailing zeroes. Mode 3 accepts
up to six uppercase printable Code A characters and space-pads shorter postcodes;
there is no silent truncation or uppercasing. Primary fields with modes 4/5 fail.

Secondary text uses Code A plus one-character shifts to B/C/D/E, with greedy
nine-digit compaction. This supports every byte, but deliberately does not optimize
persistent latches or multi-character shifts. Thus capacity is measured in encoded
codewords: 84 in mode 2/3, 93 in mode 4, 77 in mode 5. Shifted bytes generally cost
two codewords. Nine consecutive digits cost six. A payload that exceeds this
encoder's capacity fails even if an optimizing encoder could encode it.

Encoding options are the same explicit Latin-1/UTF-8/binary contracts as rMQR.
UTF-8 emits ECI 26 (two codewords). Input is nonempty and at most 4096 bytes;
capacity is validated after encoding. Result: `matrix`, `codewords`, `mode`,
`encoding`, `usedCodewords`, `capacity`.

`padMaxicodeSvg(result, size=240, title='', dark='#000', light='#fff')` renders the
33-by-30 staggered hexagonal geometry, the three circular finder rings and a
one-module quiet zone. It must not be rendered by the square-module matrix helper.
Its width is `size` pixels; native proportions determine height. This SVG is the
actual artifact rasterized by the independent scanner tests.

## Evidence and reproducible checks

Tables, placement and primary-field layouts are adapted from Zint commit
`9dd9791c43d6cde69d27e46f1c39874011aca4c3`, with BSD-3-Clause attribution retained in
[the license](../../../pad/subs/codes/_lib/SPECIALIZED-CODES-LICENSE).
Zint documents the relevant primary standards as ISO/IEC 23941:2022 and
ISO/IEC 16023:2000. This implementation claims the supported subset above.

`php tests/subsystems/code-specialized.php` checks 414 independent Zint module
matrices, including every rMQR size at both correction levels in all three
single-segment modes, capacity boundaries and primary MaxiCode modes. It also
checks input rejection, SVG escaping, hexagonal geometry and quiet zones.

`python tests/subsystems/code-specialized-oracle.py` needs numpy, pillow, zxing-cpp
and ImageMagick. It scans all fixtures plus deterministic random strings, Unicode
ECI cases and all 256 raw byte values. rMQR is scanned at 2/4/8 pixels per module
at all four rotations. Actual MaxiCode SVGs are rasterized and scanned at widths
160/320/640. ZXing's MaxiCode detector requires upright input (also confirmed on
Zint's PNG output), so its rotation tests use an explicit four-orientation adapter;
these do not establish native automatic MaxiCode rotation detection. Raster tests
are not a substitute for a physical printer/scanner certification.

Runnable examples: `/showcase/?barcode/rmqr` and `/showcase/?barcode/maxicode`.
