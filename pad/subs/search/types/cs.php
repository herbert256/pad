<?php

  // cs - Czech uses the named Dolamic–Savoy light stemmer, ported from [Apache Lucene](https://github.com/apache/lucene/blob/main/lucene/analysis/common/src/java/org/apache/lucene/analysis/cz/CzechStemmer.java), under Apache-2.0. A selected upstream parity vocabulary is committed with attribution. No invented suffixes or stopword list: stop words are deliberately disabled; shared engine Unicode tokenization and accent-folded search apply. The raw stemmer retains diacritics; cache identity includes cs and helper source.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $catalog=[['name'=>'PAD-42 identifier','link'=>'/tags/'],['name'=>'Města a městům','link'=>'/tags/']];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {search:cs 'catalog', q='města', fields='name'}<p>{$name}</p>{/search:cs}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/search-cs.php`.

include_once PAD.'subs/search/_lib/czech.php';
return ['name'=>'Czech','stopwords'=>'','stem'=>'padSearchCzechStem','files'=>['subs/search/_lib/czech.php']];
