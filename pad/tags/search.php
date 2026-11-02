<?php

  // {pad:search 'blog', q=$q, fields='title^2, body', lang='en'}...{/pad:search} - site search without
  // a search server: the rows of a collection, a data store, a page's array or a _data file
  // that the query finds, ranked by BM25 - the search subsystem, subs/search/search.php.

  return include PAD . 'subs/search/search.php';

?>
