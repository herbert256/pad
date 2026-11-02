# Search: Polish (pl)

Use `padSearch($rows, $query, ['lang'=>'pl'])` or the existing `{pad:search}` language option. Algorithm: Snowball 3.2.0, compiler/source commit `6389c79f91cd67ed2de3ebdcf84ec8c2d2d23c11`, generated with `make php_out/polish-stemmer.php`. Runtime and generated class names are PAD-prefixed; typed class constants are removed for PHP 8.1 compatibility. Source and license: https://github.com/snowballstem/snowball . Generated code is checked in; no runtime compiler/native extension/network dependency.

Stop words are a deliberately small PAD-authored common-function-word list, independent of the Snowball algorithm. Existing Unicode tokenization, accent-insensitive term variants, phrase positions and prefix expansion remain in use.

`php tests/subsystems/search-pl.php` compares every upstream vocabulary/output pair from snowball-data commit `019921c3a41c2b70063ee3feb215a00c8e594597`. Fixtures are losslessly gzip-compressed and carry upstream provenance/licensing. The generated runtime and language code are included in the search cache stamp, so code updates invalidate old indexes. Reference algorithm: https://snowballstem.org/algorithms/polish/stemmer.html .
