# search:cs

Czech uses the named Dolamic–Savoy light stemmer, ported from [Apache Lucene](https://github.com/apache/lucene/blob/main/lucene/analysis/common/src/java/org/apache/lucene/analysis/cz/CzechStemmer.java), under Apache-2.0. A selected upstream parity vocabulary is committed with attribution. No invented suffixes or stopword list: stop words are deliberately disabled; shared engine Unicode tokenization and accent-folded search apply. The raw stemmer retains diacritics; cache identity includes cs and helper source.

## Example

```pad
{search:cs 'catalog', q='města', fields='name'}{$name}{/search:cs}
```

Run the focused fixture with `php tests/subsystems/search-cs.php`.
