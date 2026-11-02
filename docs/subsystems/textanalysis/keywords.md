# textanalysis: keywords

Frequency or smoothed TF-IDF: tf=count/non-stopword document tokens; idf=log((1+document count)/(1+document frequency))+1. Corpus score sums document scores. Returns counts, document frequencies and scores; ties sort lexically. Stopwords are explicit single tokens normalized through the existing tokenizer; none are implicit. ≤500 documents, 4 MiB, 100000 tokens, 20000 vocabulary, 1000 stopwords; limit 1..1000 terms per result. Scores describe lexical salience, not semantic understanding.

Runnable example: `apps/tags/examples/textanalysis/keywords.pad`. Independent fixture: `php tests/subsystems/expansion-textanalysis-keywords.php`.
