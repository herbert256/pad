<?php

  // Site search without a search server - the search subsystem behind {pad:search} and padSearch
  // (lib/search.php). A source - a Markdown collection, a data store, a page's rows, a _data
  // file, a db() result - is made into an inverted index the first time it is searched and
  // kept in DATA/cache/search/<app>/; the index is made again when what it is made of
  // changed: the names, times and sizes of a collection's files (what a file holds too when
  // it changed in the last second), a hash of the fields searched of any other source's
  // rows, and the code that makes an index (padSearchStamp).
  //
  // A language is a file of subs/search/types/ - en, nl, de, fr, es, it, pt, sv, da, no and
  // ru - with its stop words and its Snowball stemmer; a language is added by adding its file. A text is split into words
  // by Unicode letters and digits (an apostrophe inside a word stays: l'homme, don't), each
  // word in lower case; a stop word counts for the positions only, every other word is
  // stemmed and then written without its accents - café, cafe and cafés are one term - and a
  // word with accents is stemmed without them too, its second term when the stem is another
  // (sécurité is secur and securit, securite securit), so a query finds a word in another
  // form, and without the accents it was typed with or with those it was typed without.
  //
  // The query: words (a document with any of them is found), "a phrase" (its words in that
  // order, required - "word" makes a word required), prefix* (any word that begins so), -word
  // and -"a phrase" (a document with it is left out). The ranking is BM25F: the frequency of
  // a term in each field weighted by its boost (fields='title^2, body') and normalised by the
  // field's length against its average, k1 1.2 and b 0.75, with Lucene's idf, which is never
  // negative; a prefix scores as the best word it stands for, a phrase as a term of its own.
  //
  // padSearchLanguage  the language of a code, read once per request, or an error message
  // padSearchFields    'title^2, body' as [ [ field, boost ] ...], or an error message
  // padSearchWords     a text as its words, padSearchTerms a word as its terms
  // padSearchIndex     the inverted index of rows, a string per term
  // padSearchKept      the index of a source from the cache, made again when it changed -
  //                    kept in parts (padSearchParts, padSearchWrite), of which a request
  //                    reads those its query needs (padSearchPart)
  // padSearchQuery     a query as its words, prefixes, phrases and exclusions
  // padSearchScores    the documents a query finds, with their scores, best first
  // padSearchRun       all of it: a source and a query to the rows found, each with its score
  //                    and an excerpt with the matches marked (padExcerpt, tags/_lib/excerpt.php)
  // padSearchSource    the source {pad:search 'name'} names; padSearchSign the sign of rows,
  //                    padSearchStamp the sign of the code that makes an index
  // padSearchList      the hits as the list the single {pad:search/} writes

  include_once PAD . 'subs/search/_lib/snowball.php';
  include_once PAD . 'subs/search/_lib/facets.php';
  include_once PAD . 'tags/_lib/excerpt.php';

  // Raised when the index's form changes: every kept index is then made again.

  const padSearchForm = 3;

  // A language by another code: Norwegian Bokmål and Nynorsk, the nb and nn of the locales
  // nb_NO and nn_NO, are the one Norwegian of Snowball, no.

  const padSearchAlias = [ 'nb' => 'no', 'nn' => 'no' ];

  function padSearchLanguage ( $lang ) {

    static $languages = [];

    $lang = strtolower ( trim ( (string) $lang ) );
    $lang = padSearchAlias [$lang] ?? $lang;

    if ( ! isset ( $languages [$lang] ) ) {

      if ( ! padKind ( 'search', $lang ) )
        return "there is no search language '" . padMakeSafe ( $lang, 20 ) . "' - " . implode ( ', ', padKinds ( 'search' ) );

      $language              = include PAD . "subs/search/types/$lang.php";
      $language ['code']     = $lang;
      $language ['stop']     = array_fill_keys ( preg_split ( '/\s+/', trim ( $language ['stopwords'] ) ), TRUE );
      $languages [$lang]     = $language;

    }

    return $languages [$lang];

  }

  // The language of the request's locale when it is one, else English.

  function padSearchLocale () {

    $lang = strtolower ( substr ( padLocaleOf ( '' ), 0, 2 ) );
    $lang = padSearchAlias [$lang] ?? $lang;

    return padKind ( 'search', $lang ) ? $lang : 'en';

  }

  // 'title^2, body' as [ [ 'title', 2.0 ], [ 'body', 1.0 ] ].

  function padSearchFields ( $spec ) {

    $fields = [];

    foreach ( explode ( ',', (string) $spec ) as $one ) {

      $one = trim ( $one );

      if ( $one === '' )
        continue;

      if ( ! preg_match ( '/^([A-Za-z_][A-Za-z0-9_.-]*)\s*(?:\^\s*([0-9]+(?:\.[0-9]+)?))?$/', $one, $m ) or ( isset ( $m [2] ) and (float) $m [2] <= 0 ) )
        return "fields= is a list of field names, each with a boost if it has one - title^2, body - not '" . padMakeSafe ( $one, 30 ) . "'";

      $fields [ $m [1] ] = [ $m [1], isset ( $m [2] ) ? (float) $m [2] : 1.0 ];

    }

    return $fields ? array_values ( $fields ) : 'fields= names no field';

  }

  // The fields of rows when none are named: every field that holds text - no number - in
  // any row, in the order the rows have them. The first row alone was read, and a NULL or a
  // number there - a db() result with an empty column, a book titled 1984 - left the field
  // unsearched in every row.

  function padSearchGuessFields ( $rows ) {

    $seen = $text = [];

    foreach ( (array) $rows as $row )
      foreach ( padSearchRow ( $row ) as $name => $value ) {
        $seen [$name] = TRUE;
        if ( ! isset ( $text [$name] ) and is_string ( $value ) and ! is_numeric ( $value ) and padValidVar ( (string) $name ) )
          $text [$name] = TRUE;
      }

    $fields = [];

    foreach ( array_keys ( $seen ) as $name )
      if ( isset ( $text [$name] ) )
        $fields [] = [ (string) $name, 1.0 ];

    return $fields;

  }

  // A word without its accents and marks: é is e, ß ss, æ ae, ø o, ł l, ё е - a fixed table,
  // so the index is the same with and without the intl extension.

  function padSearchFold ( $text ) {

    static $map = NULL;

    if ( $map === NULL ) {
      $map = [];
      foreach ( [ 'a' => 'àáâãäåāăą', 'c' => 'çćĉċč', 'd' => 'ďđð', 'e' => 'èéêëēĕėęě', 'g' => 'ĝğġģ', 'h' => 'ĥħ',
                  'i' => 'ìíîïĩīĭįı', 'j' => 'ĵ', 'k' => 'ķ', 'l' => 'ĺļľŀł', 'n' => 'ñńņňŉ', 'o' => 'òóôõöøōŏő',
                  'r' => 'ŕŗř', 's' => 'śŝşšș', 't' => 'ţťŧț', 'u' => 'ùúûüũūŭůűų', 'w' => 'ŵ', 'y' => 'ýÿŷ',
                  'z' => 'źżž' ] as $plain => $marked )
        foreach ( mb_str_split ( $marked ) as $char )
          $map [$char] = $plain;
      $map += [ 'ß' => 'ss', 'æ' => 'ae', 'œ' => 'oe', 'þ' => 'th', 'ĳ' => 'ij', 'ё' => 'е' ];
    }

    return strtr ( preg_replace ( '/\p{M}+/u', '', $text ), $map );

  }

  // The words of a text in lower case, with their positions: a stop word keeps its place, so
  // a phrase is found across one - "king of the hill".

  function padSearchWords ( $text ) {

    $text = mb_scrub ( (string) $text, 'UTF-8' );

    if ( class_exists ( 'Normalizer' ) )
      $text = Normalizer::normalize ( $text, Normalizer::FORM_C ) ?: $text;

    preg_match_all ( "/[\\p{L}\\p{N}\\p{M}]+(?:['’][\\p{L}\\p{N}\\p{M}]+)*/u", $text, $m );

    $words = [];

    foreach ( $m [0] as $word )
      $words [] = str_replace ( '’', "'", mb_strtolower ( mb_substr ( $word, 0, 64 ) ) );

    return $words;

  }

  // A word as its terms: none for a stop word, else its stem without accents - and the stem
  // of the word written without its accents, when that is another: a stemmer reads the
  // accents (é is a vowel and a suffix letter to Snowball's French), so the stems of
  // sécurité and securite are secur and securit, of musée and musee mus and muse, and the
  // stem alone, accents dropped after, found no word typed without them. A word with a
  // term of a query's word is found - sécurité by securite, securite by sécurité. A number
  // is as it is. The terms of a request are kept: a word met again is not stemmed again.
  //
  // A language's letters are read as others before a stop word is looked up: Snowball's
  // Russian stop words are written with е for ё, as its stemmer reads a word, so её and ещё
  // are stop words as ее and еще are.

  function padSearchTerms ( $word, $language ) {

    static $memo = [];

    $key = $language ['code'];

    if ( isset ( $memo [$key] [$word] ) )
      return $memo [$key] [$word];

    if ( isset ( $language ['stop'] [ isset ( $language ['letters'] ) ? strtr ( $word, $language ['letters'] ) : $word ] ) )
      $terms = [];
    elseif ( preg_match ( '/\p{N}/u', $word ) )
      $terms = [ padSearchFold ( $word ) ];
    else {
      $terms = [ padSearchFold ( ( $language ['stem'] ) ( $word ) ) ];
      $plain = padSearchFold ( $word );
      if ( $plain !== $word ) {
        $other = padSearchFold ( ( $language ['stem'] ) ( $plain ) );
        if ( $other !== $terms [0] )
          $terms [] = $other;
      }
    }

    if ( count ( $memo [$key] ?? [] ) > 50000 )
      $memo [$key] = [];

    return $memo [$key] [$word] = $terms;

  }

  // The text of a field: a list's items one after the other, HTML without its tags, its
  // scripts and styles - a block tag parts two words, as for an excerpt - and its entities
  // read, in plain text too: &amp; is no word amp.
  //
  // A field is HTML when it holds what only HTML holds - an end tag, a comment, a void
  // element (<br>, <img src=...>) or a start tag with a quoted attribute. Any "<word" with a
  // ">" further on was taken for HTML, and plain text lost what stood between: a<b and b>c
  // was indexed and excerpted as ac, "fits <i think> 15 inch" without "i think".

  function padSearchText ( $value ) {

    if ( is_array ( $value ) )
      return implode ( ' ', array_map ( 'padSearchText', $value ) );

    if ( ! is_scalar ( $value ) or is_bool ( $value ) )
      return '';

    $text = padUnprotect ( (string) $value );

    if ( padSearchHtml ( $text ) )
      $text = strip_tags ( preg_replace ( '#<(?=/?(?:p|div|li|h[1-6]|br|hr|tr|td|th|dt|dd|ul|ol|dl|table|caption|pre|blockquote|section|article|aside|header|footer|nav|main|figure|figcaption|address|details|summary)\b)#i', ' <', padExcerptCode ( $text ) ) );

    return str_contains ( $text, '&' ) ? html_entity_decode ( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) : $text;

  }

  function padSearchHtml ( $text ) {

    return str_contains ( $text, '<' )
       and preg_match ( '#</[a-z][a-z0-9-]*\s*>|<!--|<(?:area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr)\b[^<>]*>'
                      . '|<[a-z][a-z0-9-]*\s[^<>]*?[a-z_:][a-z0-9_:.-]*\s*=\s*(?:"[^"<>]*"|\'[^\'<>]*\')[^<>]*>#i', $text );

  }

  // The inverted index of rows, in the form it is kept in: per term one string, the
  // documents with it - "document:field:count;" for each field it stands in - then a "|"
  // and the places of the term there, "p,p,p;" in the same order, read apart only for the
  // terms a query has; the length of every field of every document as 32-bit numbers one
  // after the other (len); the average length per field; and the words met, by their
  // unaccented forms (padSearchSurface), to the terms they stand for, space between - what
  // a prefix is looked up in; one term per form kept the last, and résumé and resume, one
  // form, lost a term to resu*. Nested
  // arrays per term, document, field and place took some 185 bytes for every word of the
  // source, and the index of a few thousand rows ran out of PHP's memory on every request
  // that read it.

  function padSearchIndex ( $rows, $fields, $language ) {

    $docs = $places = $words = $met = [];
    $lens = '';
    $sums = array_fill ( 0, count ( $fields ), 0 );
    $doc  = 0;

    foreach ( $rows as $row ) {

      $row = padSearchRow ( $row );
      $len = [];

      foreach ( $fields as $f => [ $name ] ) {

        $at = [];
        $len [$f] = 0;

        foreach ( padSearchWords ( padSearchText ( padArrGet ( $row, $name, '' ) ) ) as $place => $word ) {
          $terms = padSearchTerms ( $word, $language );
          if ( ! $terms )
            continue;
          $len [$f]++;
          foreach ( $terms as $term )
            $at [$term] [] = $place;
          if ( ! isset ( $met [$word] ) ) {
            $met [$word] = TRUE;
            foreach ( padSearchSurface ( $word ) as $surface )
              foreach ( $terms as $term )
                $words [$surface] [$term] = TRUE;
          }
        }

        // Appended in place: a string built again for every document of a term made the
        // index quadratic in how many documents hold a word.

        foreach ( $at as $term => $list ) {
          $one = "$doc:$f:" . count ( $list ) . ';';
          if ( isset ( $docs [$term] ) ) {
            $docs   [$term] .= $one;
            $places [$term] .= implode ( ',', $list ) . ';';
          } else {
            $docs   [$term]  = $one;
            $places [$term]  = implode ( ',', $list ) . ';';
          }
        }

        $sums [$f] += $len [$f];

      }

      $lens .= pack ( 'N*', ...$len );
      $doc++;

    }

    $terms = [];

    foreach ( $docs as $term => $list ) {
      $terms [$term] = "$list|" . $places [$term];
      unset ( $docs [$term], $places [$term] );
    }

    foreach ( $words as $word => $set )
      $words [$word] = implode ( ' ', array_keys ( $set ) );

    return [ 'form' => padSearchForm, 'lang' => $language ['code'], 'fields' => array_column ( $fields, 0 ), 'n' => $doc,
             'avg' => array_map ( fn ( $sum ) => $doc ? $sum / $doc : 0, $sums ), 'len' => $lens, 'terms' => $terms, 'words' => $words ];

  }

  // A row as an array: an object's properties, a scalar as the one field value.

  function padSearchRow ( $row ) {

    if ( is_object ( $row ) )
      return get_object_vars ( $row );

    return is_array ( $row ) ? $row : [ 'value' => $row ];

  }

  // The index of a source, kept in DATA/cache/search/<app>/<key>/<sign>/ - the key made of
  // the source's name, the fields and the language, the sign of what the source is now (the
  // files of a collection, a hash of the rows) and of the code. An index kept for
  // another sign is made again by $make, which answers [ rows, index ], and the one it
  // replaces is swept. A request reads what a query needs and no more: head.json (the
  // number of documents, the fields, their average lengths, how the terms and words are
  // spread), len.bin (the lengths), the buckets t<n>.json of the query's terms - a term's
  // bucket a hash of it - the shard w<n>.json of a prefix's first letter, and rows.json,
  // the rows of a collection, which are kept with the index: reading them means writing
  // every file's Markdown as HTML. The whole index was one file read in full by every
  // request. The files are written in a directory of their own, renamed into place when
  // complete: a reader finds a whole index or none. An index that cannot be written is
  // used from memory.

  function padSearchKept ( $name, $fields, $language, $sign, $make, $keepRows, $again = FALSE ) {

    static $read = [];

    $key = 'cache/search/' . $GLOBALS ['padApp'] . '/' . md5 ( "$name\n" . json_encode ( $fields ) . "\n" . $language ['code'] ) . '/';
    $dir = $key . md5 ( $sign ) . '/';

    if ( ! $again and isset ( $read [$dir] ) )
      return $read [$dir];

    if ( ! $again and is_file ( DATA . $dir . 'head.json' ) ) {

      $head = json_decode ( (string) @file_get_contents ( DATA . $dir . 'head.json' ), TRUE );
      $len  = @file_get_contents ( DATA . $dir . 'len.bin' );

      if ( is_array ( $head ) and ( $head ['form'] ?? 0 ) === padSearchForm and is_string ( $len )
           and strlen ( $len ) === $head ['n'] * count ( $head ['fields'] ) * 4 )
        return $read [$dir] = [ 'dir' => $dir, 'len' => $len, 'f' => count ( $head ['fields'] ) ] + $head;

    }

    [ $rows, $index ] = $make ();

    $parts = padSearchParts ( $index );
    $head  = [ 'form' => padSearchForm, 'lang' => $index ['lang'], 'fields' => $index ['fields'], 'n' => $index ['n'],
               'avg' => $index ['avg'], 'buckets' => count ( $parts ['t'] ), 'shards' => count ( $parts ['w'] ) ];
    $kept  = [ 'dir' => $dir, 'len' => $index ['len'], 'f' => count ( $index ['fields'] ) ] + $head;

    unset ( $index );

    // Made again for a part that was gone: what stands of the index goes first, or the new
    // one could never take its place.

    if ( $again )
      padSearchDrop ( DATA . $dir );

    if ( ! padSearchWrite ( $key, $dir, $head, $kept ['len'], $parts, $keepRows ? array_values ( (array) $rows ) : NULL ) )
      $kept ['mem'] = $parts + [ 'rows' => $keepRows ? array_values ( (array) $rows ) : NULL ];

    return $read [$dir] = $kept;

  }

  // The terms of an index in buckets and its words in shards, each some 64 kB at the most -
  // their number a power of two, a term's bucket the low bits of its crc32, a word's shard
  // those of its first letter, so a prefix reads the one shard of its first letter.

  function padSearchParts ( $index ) {

    $size = 0;
    foreach ( $index ['terms'] as $term => $posting )
      $size += strlen ( $posting ) + strlen ( $term ) + 6;

    for ( $buckets = 1; $buckets < 4096 and $size / $buckets > 65536; $buckets *= 2 );

    $size = 0;
    foreach ( $index ['words'] as $word => $term )
      $size += strlen ( $word ) + strlen ( $term ) + 6;

    for ( $shards = 1; $shards < 256 and $size / $shards > 65536; $shards *= 2 );

    $parts = [ 't' => array_fill ( 0, $buckets, [] ), 'w' => array_fill ( 0, $shards, [] ) ];

    foreach ( $index ['terms'] as $term => $posting )
      $parts ['t'] [ crc32 ( (string) $term ) & ( $buckets - 1 ) ] [$term] = $posting;

    foreach ( $index ['words'] as $word => $term )
      $parts ['w'] [ crc32 ( mb_substr ( (string) $word, 0, 1 ) ) & ( $shards - 1 ) ] [$word] = $term;

    return $parts;

  }

  // The files of an index in a directory of their own, renamed to its place when they are
  // all there, and the indexes kept before for the key swept - a build another request left
  // unfinished once it is ten minutes old. FALSE when the index is not there to read.

  function padSearchWrite ( $key, $dir, $head, $len, $parts, $rows ) {

    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
    $files = [ 'len.bin' => $len ];

    foreach ( [ 't', 'w' ] as $kind )
      foreach ( $parts [$kind] as $n => $part )
        $files ["$kind$n.json"] = json_encode ( $part, $flags | JSON_FORCE_OBJECT );

    if ( $rows !== NULL )
      $files ['rows.json'] = json_encode ( $rows, $flags | JSON_PRESERVE_ZERO_FRACTION );

    $files ['head.json'] = json_encode ( $head, $flags | JSON_PRESERVE_ZERO_FRACTION );

    if ( in_array ( FALSE, $files, TRUE ) )
      return FALSE;

    $build = $key . '.b' . padRandomString ( 8 ) . '/';

    foreach ( $files as $file => $data )
      padFilePut ( $build . $file, $data );

    if ( ! is_dir ( DATA . $build ) )
      return FALSE;

    if ( ! @rename ( DATA . $build, DATA . $dir ) )
      padSearchDrop ( DATA . $build );

    foreach ( scandir ( DATA . $key ) ?: [] as $entry )
      if ( $entry != '.' and $entry != '..' and "$key$entry/" != $dir
           and ( $entry [0] != '.' or ( str_starts_with ( $entry, '.b' ) and @filemtime ( DATA . $key . $entry ) < time () - 600 ) ) )
        padSearchDrop ( DATA . $key . $entry );

    return is_file ( DATA . $dir . 'head.json' );

  }

  function padSearchDrop ( $dir ) {

    if ( ! is_dir ( $dir ) or is_link ( $dir ) )
      return;

    foreach ( scandir ( $dir ) ?: [] as $file )
      if ( $file != '.' and $file != '..' )
        @unlink ( "$dir/$file" );

    @rmdir ( $dir );

  }

  // A bucket of terms ('t') or a shard of words ('w') of a kept index: from memory for an
  // index that could not be written, else its file - the files read kept for the request,
  // eight megabytes of them at the most. A file that is gone - swept by a request that made
  // the index again for a source changed meanwhile - is noted, and padSearchRun makes the
  // index again itself.

  function padSearchPart ( $index, $kind, $n ) {

    static $parts = [], $size = 0;

    if ( isset ( $index ['mem'] ) )
      return $index ['mem'] [$kind] [$n] ?? [];

    $file = $index ['dir'] . "$kind$n.json";

    if ( isset ( $parts [$file] ) )
      return $parts [$file];

    $json = @file_get_contents ( DATA . $file );

    if ( $json === FALSE ) {
      padSearchLost ( TRUE );
      return [];
    }

    if ( $size > 8000000 )
      $parts = [];

    $size += strlen ( $json );

    return $parts [$file] = json_decode ( $json, TRUE ) ?? [];

  }

  function padSearchLost ( $lost = NULL ) {

    static $is = FALSE;

    if ( $lost !== NULL )
      $is = $lost;

    return $is;

  }

  // The rows kept with the index of a collection, NULL when they are gone.

  function padSearchKeptRows ( $index ) {

    if ( isset ( $index ['mem'] ) )
      return $index ['mem'] ['rows'];

    $rows = json_decode ( (string) @file_get_contents ( DATA . $index ['dir'] . 'rows.json' ), TRUE );

    return is_array ( $rows ) ? $rows : NULL;

  }

  // The length of a field of a document, from the 32-bit numbers of len.

  function padSearchLen ( $index, $doc, $f ) {

    return unpack ( 'N', $index ['len'], ( $doc * $index ['f'] + $f ) * 4 ) [1];

  }

  // A query as [ words, prefixes, phrases, out ]: a word as its terms, a prefix as the terms
  // of the words it begins, a phrase as the terms of each word with its place after its
  // first, out as the words, prefixes and phrases that leave a document out. A word that is
  // a stop word is dropped; so is a phrase of them only. Fifty parts of a thousand
  // characters at the most - a query is typed, not posted.

  function padSearchQuery ( $q, $language, $index ) {

    $query = [ 'words' => [], 'prefixes' => [], 'phrases' => [], 'out' => [ 'words' => [], 'prefixes' => [], 'phrases' => [] ] ];
    $q     = mb_substr ( mb_scrub ( padUnprotect ( (string) $q ), 'UTF-8' ), 0, 1000 );

    preg_match_all ( '/(-?)(?:"([^"]*)"?|([^\s"]+))/u', $q, $parts, PREG_SET_ORDER );

    foreach ( array_slice ( $parts, 0, 50 ) as $part ) {

      $out = ( $part [1] === '-' );
      $to  = &$query;

      if ( $out )
        $to = &$query ['out'];

      if ( ! isset ( $part [3] ) ) {

        $phrase = [];
        $first  = NULL;

        foreach ( padSearchWords ( $part [2] ) as $at => $word ) {
          $terms = padSearchTerms ( $word, $language );
          if ( ! $terms )
            continue;
          $first   ??= $at;
          $phrase [] = [ $terms, $at - $first ];
        }

        if ( $phrase )
          $to ['phrases'] [ json_encode ( $phrase ) ] = $phrase;

      } elseif ( preg_match ( '/^(.*?)\*+$/u', $part [3], $star ) ) {

        foreach ( padSearchWords ( $star [1] ) as $word )
          if ( mb_strlen ( $word ) >= 1 ) {
            $prefix = padSearchFold ( $word );
            $to ['prefixes'] [$prefix] = padSearchExpand ( $prefix, $index );
          }

      } else

        foreach ( padSearchWords ( $part [3] ) as $word )
          if ( $terms = padSearchTerms ( $word, $language ) )
            $to ['words'] [ implode ( ' ', $terms ) ] = $terms;

      unset ( $to );

    }

    return $query;

  }

  // A query as its text: a list - ?q[]=a&q[]=b through padRequest - its values one after the
  // other, anything else that is no value nothing.

  function padSearchQueryText ( $q ) {

    if ( is_array ( $q ) ) {
      $words = [];
      array_walk_recursive ( $q, function ( $value ) use ( &$words ) {
        if ( is_scalar ( $value ) )
          $words [] = (string) $value;
      } );
      return implode ( ' ', $words );
    }

    return is_scalar ( $q ) ? (string) $q : '';

  }

  // The terms of the words that begin with a prefix, read from the shard of the words of
  // its first letter.

  function padSearchExpand ( $prefix, $index ) {

    $terms = [];

    foreach ( padSearchPart ( $index, 'w', crc32 ( mb_substr ( $prefix, 0, 1 ) ) & ( $index ['shards'] - 1 ) ) as $word => $set )
      if ( str_starts_with ( (string) $word, $prefix ) )
        foreach ( explode ( ' ', (string) $set ) as $term )
          $terms [$term] = TRUE;

    return array_keys ( $terms );

  }

  // The forms a prefix finds a word by, without accents: the word, and of a word with an
  // apostrophe its longest part too - université of l'université, as the excerpt marks
  // it. The word alone was kept, and univ* missed l'université.

  function padSearchSurface ( $word ) {

    $surface = [ padSearchFold ( $word ) ];

    if ( str_contains ( $word, "'" ) ) {
      $parts = explode ( "'", $word );
      usort ( $parts, fn ( $a, $b ) => mb_strlen ( $b ) <=> mb_strlen ( $a ) );
      $surface [] = padSearchFold ( $parts [0] );
    }

    return $surface;

  }

  // The kept string of a term, '' when no document has it.

  function padSearchPosting ( $index, $term ) {

    $term = (string) $term;

    return padSearchPart ( $index, 't', crc32 ( $term ) & ( $index ['buckets'] - 1 ) ) [$term] ?? '';

  }

  // The documents of a term as [ document => [ field => count ] ].

  function padSearchPostings ( $index, $term ) {

    $posting = padSearchPosting ( $index, $term );
    $docs    = [];

    if ( $posting === '' )
      return $docs;

    foreach ( explode ( ';', substr ( $posting, 0, strpos ( $posting, '|' ) - 1 ) ) as $entry ) {
      [ $doc, $f, $count ] = explode ( ':', $entry );
      $docs [ (int) $doc ] [ (int) $f ] = (int) $count;
    }

    return $docs;

  }

  // The places of a term as [ document => [ field => 'p,p,p' ] ].

  function padSearchPlaces ( $index, $term ) {

    $posting = padSearchPosting ( $index, $term );
    $docs    = [];

    if ( $posting === '' )
      return $docs;

    [ $list, $places ] = explode ( '|', $posting, 2 );

    $places = explode ( ';', rtrim ( $places, ';' ) );

    foreach ( explode ( ';', rtrim ( $list, ';' ) ) as $i => $entry ) {
      [ $doc, $f ] = explode ( ':', $entry );
      $docs [ (int) $doc ] [ (int) $f ] = $places [$i];
    }

    return $docs;

  }

  // A phrase is found where each next word stands at its place after the first: the places
  // of a word - of any of its terms - are looked up by key, flipped once per field: in_array
  // went through them all for every place of the first word, and a word standing thousands
  // of times in one long field made a phrase take seconds. [ document => [ field => count ] ].

  function padSearchPhrase ( $index, $phrase ) {

    $docs = [];
    $next = [];

    for ( $i = 1; $i < count ( $phrase ); $i++ )
      $next [$i] = padSearchWordPlaces ( $index, $phrase [$i] [0] );

    foreach ( padSearchWordPlaces ( $index, $phrase [0] [0] ) as $doc => $fields )
      foreach ( $fields as $f => $places ) {

        for ( $i = 1; $i < count ( $phrase ); $i++ )
          if ( ! isset ( $next [$i] [$doc] [$f] ) )
            continue 2;

        $count = 0;

        foreach ( $places as $at => $one ) {
          $whole = TRUE;
          for ( $i = 1; $i < count ( $phrase ) and $whole; $i++ )
            $whole = isset ( $next [$i] [$doc] [$f] [ $at + $phrase [$i] [1] ] );
          if ( $whole )
            $count++;
        }

        if ( $count )
          $docs [$doc] [$f] = $count;

      }

    return $docs;

  }

  // The places of a word of a query - of each of its terms - as [ document => [ field =>
  // [ place => TRUE ] ] ].

  function padSearchWordPlaces ( $index, $terms ) {

    $docs = [];

    foreach ( $terms as $term )
      foreach ( padSearchPlaces ( $index, $term ) as $doc => $fields )
        foreach ( $fields as $f => $places )
          $docs [$doc] [$f] = ( $docs [$doc] [$f] ?? [] ) + array_fill_keys ( explode ( ',', $places ), TRUE );

    return $docs;

  }

  // BM25F of one term - its postings - over the documents it is in: [ document => score ].

  function padSearchBm25 ( $index, $boosts, $postings ) {

    $k1     = 1.2;
    $b      = 0.75;
    $n      = $index ['n'];
    $df     = count ( $postings );
    $idf    = log ( 1 + ( $n - $df + 0.5 ) / ( $df + 0.5 ) );
    $scores = [];

    foreach ( $postings as $doc => $fields ) {

      $tf = 0.0;

      foreach ( $fields as $f => $count ) {
        $avg = $index ['avg'] [$f] ?: 1;
        $tf += $boosts [$f] * $count / ( 1 - $b + $b * padSearchLen ( $index, $doc, $f ) / $avg );
      }

      $scores [$doc] = $idf * $tf * ( $k1 + 1 ) / ( $tf + $k1 );

    }

    return $scores;

  }

  // The documents a query finds, [ document => score ], the best first and of two equal
  // scores the first in the source first. A document with no word, prefix or phrase of the
  // query is not found; one without every phrase, or with anything the query leaves out,
  // neither.

  function padSearchScores ( $index, $boosts, $query ) {

    $scores = [];
    $must   = [];
    $not    = [];

    // A word of the query scores as the best of its terms, a prefix as the best word it
    // stands for.

    foreach ( array_merge ( array_values ( $query ['words'] ), array_values ( $query ['prefixes'] ) ) as $terms ) {
      $best = [];
      foreach ( $terms as $term )
        foreach ( padSearchBm25 ( $index, $boosts, padSearchPostings ( $index, $term ) ) as $doc => $score )
          $best [$doc] = max ( $best [$doc] ?? 0, $score );
      foreach ( $best as $doc => $score )
        $scores [$doc] = ( $scores [$doc] ?? 0 ) + $score;
    }

    foreach ( $query ['phrases'] as $phrase ) {
      $found = padSearchBm25 ( $index, $boosts, padSearchPhrase ( $index, $phrase ) );
      $must [] = $found;
      foreach ( $found as $doc => $score )
        $scores [$doc] = ( $scores [$doc] ?? 0 ) + $score;
    }

    foreach ( array_merge ( array_values ( $query ['out'] ['words'] ), array_values ( $query ['out'] ['prefixes'] ) ) as $terms )
      foreach ( $terms as $term )
        $not += padSearchPostings ( $index, $term );

    foreach ( $query ['out'] ['phrases'] as $phrase )
      $not += padSearchPhrase ( $index, $phrase );

    foreach ( $scores as $doc => $score ) {
      $keep = ! isset ( $not [$doc] );
      foreach ( $must as $found )
        $keep = $keep && isset ( $found [$doc] );
      if ( ! $keep )
        unset ( $scores [$doc] );
    }

    $order = array_keys ( $scores );

    usort ( $order, fn ( $a, $b ) => [ - round ( $scores [$a], 9 ), $a ] <=> [ - round ( $scores [$b], 9 ), $b ] );

    $sorted = [];

    foreach ( $order as $doc )
      $sorted [$doc] = $scores [$doc];

    return $sorted;

  }

  // The words of a text that a query matched, as written there: what the excerpt marks. Of a
  // word with an apostrophe its longest part - homme of l'homme: the excerpt marks every word
  // that begins with a part, and l would mark them all.

  function padSearchMatched ( $text, $language, $query ) {

    $terms = [];

    foreach ( array_merge ( array_values ( $query ['words'] ), array_values ( $query ['prefixes'] ) ) as $list )
      $terms += array_fill_keys ( $list, TRUE );

    foreach ( $query ['phrases'] as $phrase )
      foreach ( $phrase as [ $list ] )
        $terms += array_fill_keys ( $list, TRUE );

    $words = [];

    foreach ( padSearchWords ( $text ) as $word )
      if ( array_intersect_key ( array_flip ( padSearchTerms ( $word, $language ) ), $terms ) ) {
        $parts = explode ( "'", $word );
        usort ( $parts, fn ( $a, $b ) => mb_strlen ( $b ) <=> mb_strlen ( $a ) );
        $words [ $parts [0] ] = TRUE;
      }

    return implode ( ' ', array_keys ( $words ) );

  }

  // A source and a query to the rows found: the source's rows - their own fields - with
  // score (BM25F, three decimals) and excerpt (HTML: the words around the first match, every
  // match in a <mark>) added, the best first. $source is [ name, sign, rows or a function
  // that answers them, keep the rows with the index, the fields when none are given ('' for
  // every field with text in a row - none is an error, no rows find nothing) ] - the sign
  // NULL for rows, signed here on the fields searched; the options are fields, lang, words
  // (of the excerpt, 30), excerpt (the field it is cut from, the last of fields), first
  // (the best rows only), excerpts (an excerpt for the first rows only, the others '') and
  // style (TRUE: every excerpt carries the style of its marks, for padSearch, whose rows the
  // page's PHP prints as it likes; the tag writes the style once itself). An error is
  // answered as a string.

  function padSearchRun ( $source, $q, $options ) {

    padSearchInfo ( [ 'total' => 0, 'facets' => [] ] );
    $filters = padSearchFilterSpec ( $options ['filters'] ?? [] );
    if ( is_string ( $filters ) )
      return $filters;
    $facets = padSearchFacetFields ( $options ['facets'] ?? [] );
    if ( is_string ( $facets ) )
      return $facets;
    padSearchInfo ( [ 'total' => 0, 'facets' => array_fill_keys ( $facets, [] ) ] );

    [ $name, $sign, $rows, $keepRows, $fieldsDefault ] = $source + [ 4 => '' ];

    // Filter the corpus before indexing/ranking: excluded documents do not affect BM25.
    // Read the current metadata even when a collection index was previously cached.
    if ( $filters ) {
      if ( is_callable ( $rows ) )
        $rows = $rows ();
      $rows = padSearchFilterRows ( $rows, $filters );
      $name .= ':filters:' . hash ( 'sha256', serialize ( $filters ) );
      $sign = NULL;
      $keepRows = FALSE;
    }

    $language = padSearchLanguage ( $options ['lang'] ?? padSearchLocale () );

    if ( is_string ( $language ) )
      return $language;

    if ( ( $options ['fields'] ?? '' ) !== '' or $fieldsDefault !== '' )
      $fields = padSearchFields ( ( $options ['fields'] ?? '' ) !== '' ? $options ['fields'] : $fieldsDefault );
    else {
      if ( is_callable ( $rows ) )
        $rows = $rows ();
      if ( ! $rows )
        return [];
      $fields = padSearchGuessFields ( $rows ) ?: 'no field of the rows holds text to search - name the fields with fields=';
    }

    if ( is_string ( $fields ) )
      return $fields;

    $words = $options ['words'] ?? 30;

    if ( ! ctype_digit ( (string) $words ) or (int) $words < 1 )
      return "words= is the length of the excerpt, a number of words - not '" . padMakeSafe ( $words, 20 ) . "'";

    $first    = $options ['first']    ?? NULL;
    $excerpts = $options ['excerpts'] ?? NULL;

    if ( $first !== NULL and ! ctype_digit ( (string) $first ) )
      return "first is a number of rows, the best that come back - not '" . padMakeSafe ( $first, 20 ) . "'";

    $excerpt = (string) ( $options ['excerpt'] ?? end ( $fields ) [0] ?? '' );

    // Rows are signed here, on the fields the index is made of; a collection by its files.

    if ( $sign === NULL ) {
      if ( is_callable ( $rows ) )
        $rows = $rows ();
      $sign = padSearchSign ( $rows, $fields );
    }

    $sign = padSearchStamp ( $language, $keepRows ) . "\n$sign";

    $make = function () use ( &$rows, $fields, $language ) {
      if ( is_callable ( $rows ) )
        $rows = $rows ();
      return [ $rows, padSearchIndex ( $rows, $fields, $language ) ];
    };

    // A part of the kept index gone while it was read - swept by a request that made it
    // again for a source changed meanwhile - and the index is made again here, in memory.

    padSearchLost ( FALSE );

    $index  = padSearchKept ( $name, $fields, $language, $sign, $make, $keepRows );
    $query  = padSearchQuery ( $q, $language, $index );
    $scores = padSearchScores ( $index, array_column ( $fields, 1 ), $query );

    if ( padSearchLost () ) {
      $index  = padSearchKept ( $name, $fields, $language, $sign, $make, $keepRows, TRUE );
      $query  = padSearchQuery ( $q, $language, $index );
      $scores = padSearchScores ( $index, array_column ( $fields, 1 ), $query );
    }

    if ( ! $scores )
      return [];

    if ( $keepRows )
      $rows = padSearchKeptRows ( $index ) ?? ( is_callable ( $rows ) ? $rows () : $rows );
    elseif ( is_callable ( $rows ) )
      $rows = $rows ();

    $rows = array_values ( (array) $rows );
    $matched = $facets ? array_intersect_key ( $rows, $scores ) : [];
    padSearchInfo ( [ 'total' => count ( $scores ), 'facets' => padSearchFacetCounts ( $matched, $facets ) ] );

    // The rows found are cut to first before anything is made of them, and an excerpt is
    // made for the first excerpts rows only - the rows a page can show: every hit had its
    // excerpt made, and a search cost what its hits cost, not what the page showed.

    if ( $first !== NULL )
      $scores = array_slice ( $scores, 0, (int) $first, TRUE );

    $rows  = array_values ( (array) $rows );
    $found = [];
    $style = ( $options ['style'] ?? FALSE ) ? padSearchExcerptStyle () : '';

    padSearchQuiet ( TRUE );

    foreach ( $scores as $doc => $score ) {

      $row = padSearchRow ( $rows [$doc] ?? [] );

      $row ['score'] = round ( $score, 3 );

      if ( $excerpts === NULL or count ( $found ) < $excerpts ) {
        $text = padSearchText ( padArrGet ( $row, $excerpt, '' ) );
        $row ['excerpt'] = $style . padExcerpt ( $text, (int) $words, padSearchMatched ( $text, $language, $query ), FALSE, '…' );
      } else
        $row ['excerpt'] = '';

      $found [] = $row;

    }

    padSearchQuiet ( FALSE );

    return $found;

  }

  // The source of {pad:search 'name'}: the Markdown collection of the name (_content/<name>/),
  // else a {pad:data} store, a page's array, a field or a _data file of the name - as data=
  // finds one for {chart:bar}. [ name, sign (NULL for rows: padSearchRun signs them on the
  // fields searched), rows, keep the rows, the fields when none are given ] - or an error
  // message. A collection is searched in title^2, body by default.

  function padSearchSource ( $name ) {

    global $padDataStore, $pqStore;

    $name = trim ( (string) $name );

    if ( $name === '' )
      return "the search needs a source - {pad:search 'blog'} searches _content/blog/, a data store, a page's array or a _data file of the name";

    if ( $dir = padCollectionDir ( $name ) ) {

      $files = glob ( $dir . '*.md' ) ?: [];
      sort ( $files );

      // A file changed in the second it was signed in - or the one before - is signed by
      // what it holds as well: a time to the second and a size alone kept an index of a file
      // edited to the same size within that second, and the rows kept with it, for good.

      $sign = [];
      $now  = time ();
      foreach ( $files as $file ) {
        $time    = (int) @filemtime ( $file );
        $sign [] = basename ( $file ) . ' ' . $time . ' ' . @filesize ( $file ) . ( $time >= $now - 1 ? ' ' . @md5_file ( $file ) : '' );
      }

      return [ "collection:$dir", md5 ( implode ( "\n", $sign ) ), fn () => padCollection ( $name ), TRUE, 'title^2, body' ];

    }

    $rows = NULL;

    if ( padValidVar ( $name ) ) {
      if     ( isset ( $padDataStore [$name] ) )                             $rows = $padDataStore [$name];
      elseif ( isset ( $pqStore [$name] ) )                                  $rows = $pqStore [$name];
      elseif ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) ) $rows = $GLOBALS [$name];
      elseif ( padFieldCheck ( $name ) and is_array ( $field = padFieldValue ( $name ) ) ) $rows = $field;
      elseif ( $file = padDataFileName ( $name ) )                          $rows = padDataFileData ( $file );
    }

    if ( ! is_array ( $rows ) )
      return "there is nothing to search named '" . padMakeSafe ( $name, 40 ) . "' - no collection _content/" . padMakeSafe ( $name, 40 ) . "/, no data store, page array or _data file of the name";

    return [ "rows:$name", NULL, $rows, FALSE, '' ];

  }

  // How many of the rows found a page can show, for the handling options a {pad:search} pair
  // carries: when they keep the first rows only - first=, page= and rows= - the rows past
  // the last page, or the first, are never shown and need no excerpt. NULL when an option
  // can reorder or filter the rows - sort=, where=, last= ... - or a callback reads them.

  function padSearchShown ( $options ) {

    $keep = [ 'first', 'page', 'rows', 'orderly', 'duplicates', 'atLeastOnce', 'left', 'right', 'both', 'sum', 'avg', 'min', 'max' ];

    foreach ( $options as $name => $value )
      if ( $name === 'callback' or ( is_string ( $name ) and ! in_array ( $name, $keep, TRUE ) and padValidVar ( $name ) and padFileExists ( PAD . "handling/types/$name.php" ) ) )
        return NULL;

    $count = fn ( $value ) => ( $value === TRUE or ! ctype_digit ( (string) $value ) ) ? 1 : (int) $value;
    $shown = NULL;

    if ( isset ( $options ['first'] ) )
      $shown = $count ( $options ['first'] );

    if ( isset ( $options ['page'] ) or isset ( $options ['rows'] ) ) {
      $page  = max ( 1, (int) ( $options ['page'] ?? 1 ) ) * (int) ( $options ['rows'] ?? 10 );
      $shown = min ( $shown ?? $page, $page );
    }

    return $shown;

  }

  // padExcerpt writes the style of the marks in front of the first excerpt of a page, and
  // that once is spent whether the excerpt is printed or not: made for the first row found
  // and dropped by first= after a sort, or never printed, it took the style with it - and
  // from every later {pad:excerpt} of the page. The excerpts of a search are made in a scope of
  // their own (padSearchQuiet), where the style was written already, so they carry none
  // and spend nothing; what prints them prints the style (padSearchExcerptStyle, made in a
  // scope of its own as well): the {pad:search} pair before its first row, the single tag
  // before its list, padSearch in every excerpt.

  function padSearchQuiet ( $on ) {

    global $padOnceScope;

    static $was = [];

    if ( $on ) {
      $was [] = $padOnceScope ?? NULL;
      $padOnceScope = ( $padOnceScope ?? '' ) . '|search';
      padExcerptStyle ();
    } else
      $padOnceScope = array_pop ( $was );

  }

  function padSearchExcerptStyle () {

    global $padOnceScope;

    static $style = NULL;

    if ( $style === NULL ) {
      $was          = $padOnceScope ?? NULL;
      $padOnceScope = ( $was ?? '' ) . '|search-style';
      $style        = padExcerptStyle ();
      $padOnceScope = $was;
    }

    return $style;

  }

  // The hits as the single {pad:search/} writes them: a list, each item the title field - a link
  // when link= names a field with one that goes nowhere a script runs - and the excerpt.

  function padSearchList ( $rows, $title, $link ) {

    include_once PAD . 'subs/calendars/_lib/calendar.php';

    $h    = fn ( $t ) => htmlspecialchars ( padUnprotectQuotes ( (string) $t ), ENT_QUOTES, 'UTF-8' );
    $html = padSearchStyle () . padExcerptStyle () . '<ol class="pad-search">';

    foreach ( $rows as $row ) {
      $text  = $h ( padSearchText ( padArrGet ( $row, $title, '' ) ) );
      $href  = ( $link !== '' ) ? (string) padSearchText ( padArrGet ( $row, $link, '' ) ) : '';
      $html .= '<li>' . ( $href !== '' && padCalendarSafeLink ( $href ) ? '<a href="' . $h ( $href ) . "\">$text</a>" : "<b>$text</b>" )
             . '<div>' . $row ['excerpt'] . '</div></li>';
    }

    return "$html</ol>";

  }

  function padSearchStyle () {

    if ( ! padWidgetOnce ( 'style:search' ) )
      return '';

    return '<style' . padStyleNonce () . '>'
         . ':where(.pad-search){padding-left:1.4em}'
         . ':where(.pad-search li){margin:0 0 10px}'
         . ':where(.pad-search li div){font-size:.92em;opacity:.85}'
         . '</style>';

  }

  // The sign of rows: a hash of the values of the fields searched, row by row - what the
  // index is made of. Every column signed them, and a column the search never reads - the
  // stock of a product, a count of views - made and wrote the whole index again whenever it
  // changed.

  function padSearchSign ( $rows, $fields ) {

    $values = [];

    foreach ( (array) $rows as $row ) {
      $row = padSearchRow ( $row );
      $one = [];
      foreach ( $fields as [ $field ] )
        $one [] = padArrGet ( $row, $field, '' );
      $values [] = $one;
    }

    return md5 ( (string) json_encode ( $values, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE ) );

  }

  // The code an index is made with - this library, the stemmers' runtime, the language's
  // file, and for a collection the reader of its files and padMarkdown - as a hash, part of
  // the sign of every index kept: a stemmer or a stop word changed, or how a text is split
  // into words, and the indexes kept are made again. Only padSearchForm said so, and it was
  // raised for a change of the index's form alone.

  function padSearchStamp ( $language, $collection ) {

    static $stamps = [];

    $key = $language ['code'] . ( $collection ? ':collection' : '' );

    if ( ! isset ( $stamps [$key] ) ) {

      $files = [ 'subs/search/_lib/search.php', 'subs/search/_lib/snowball.php', 'subs/search/types/' . $language ['code'] . '.php', 'tags/_lib/excerpt.php' ];

      if ( $collection )
        array_push ( $files, 'lib/collection.php', 'lib/markdown.php', 'tags/_lib/markdown.php' );

      $stamps [$key] = md5 ( padSearchForm . implode ( '', array_map ( fn ( $file ) => (string) @md5_file ( PAD . $file ), $files ) ) );

    }

    return $stamps [$key];

  }

?>
