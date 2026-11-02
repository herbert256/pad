# Search: Hungarian (hu)

Use `padSearch($rows, $query, ['lang'=>'hu'])`. Native generated PHP, with no runtime downloads or compiler. Snowball 3.2.0 source/compiler commit `6389c79f91cd67ed2de3ebdcf84ec8c2d2d23c11`; reproduce with `make php_out/hungarian-stemmer.php`, prefix the generated class/runtime names with PAD's names and remove typed constants for PHP 8.1 compatibility.

Algorithm: https://snowballstem.org/algorithms/hungarian/stemmer.html . Stop words: https://snowballstem.org/algorithms/hungarian/stop.txt (198 entries; comments stripped, original spelling retained). Snowball BSD licensing applies; see the shared generated runtime license and fixture COPYING. Romanian stopwords retain historical cedilla spelling; the search tokenizer's normalization supplies equivalent accent-insensitive forms.

`php tests/subsystems/search-hu.php` replays every upstream vocabulary/output pair from snowball-data commit `019921c3a41c2b70063ee3feb215a00c8e594597`. Compressed fixtures are checked in. Generated algorithm and runtime are in the index cache stamp, so changes invalidate old indexes. Existing phrase/prefix scoring remains shared.
