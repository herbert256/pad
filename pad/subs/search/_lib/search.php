<?php

  // Site search without a search server - the search subsystem behind {search} and padSearch
  // (lib/search.php). A source - a Markdown collection, a data store, a page's rows, a _data
  // file, a db() result - is made into an inverted index the first time it is searched and
  // kept in DATA/cache/search/<app>/; the index is made again when the source changed: the
  // names, times and sizes of a collection's files, a hash of the rows of any other source.
  //
  // A language is a file of subs/search/types/ - en, nl, de, fr, es - with its stop words and
  // its Snowball stemmer; a language is added by adding its file. A text is split into words
  // by Unicode letters and digits (an apostrophe inside a word stays: l'homme, don't), each
  // word in lower case; a stop word counts for the positions only, every other word is
  // stemmed and then written without its accents - café, cafe and cafés are one term - so a
  // query finds a word in another form, and without the accents it was typed without.
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
  // padSearchWords     a text as its words, padSearchTerm a word as its term
  // padSearchIndex     the inverted index of rows
  // padSearchKept      the index of a source from the cache, made again when it changed
  // padSearchQuery     a query as its words, prefixes, phrases and exclusions
  // padSearchScores    the documents a query finds, with their scores, best first
  // padSearchRun       all of it: a source and a query to the rows found, each with its score
  //                    and an excerpt with the matches marked (padExcerpt, tags/_lib/excerpt.php)
  // padSearchSource    the source {search 'name'} names; padSearchSign the sign of rows
  // padSearchList      the hits as the list the single {search/} writes

  include_once PAD . 'subs/search/_lib/snowball.php';
  include_once PAD . 'tags/_lib/excerpt.php';

  // Raised when the index's form changes: every kept index is then made again.

  const padSearchForm = 1;

  function padSearchLanguage ( $lang ) {

    static $languages = [];

    $lang = strtolower ( trim ( (string) $lang ) );

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

  // The fields of rows when none are named: every field of the first row that holds text.

  function padSearchGuessFields ( $rows ) {

    $fields = [];

    foreach ( (array) ( reset ( $rows ) ?: [] ) as $name => $value )
      if ( is_string ( $value ) and ! is_numeric ( $value ) and padValidVar ( (string) $name ) )
        $fields [] = [ (string) $name, 1.0 ];

    return $fields;

  }

  // A word without its accents and marks: é is e, ß ss, æ ae, ø o, ł l - a fixed table, so
  // the index is the same with and without the intl extension.

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
      $map += [ 'ß' => 'ss', 'æ' => 'ae', 'œ' => 'oe', 'þ' => 'th', 'ĳ' => 'ij' ];
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

  // A word as its term: '' for a stop word, else its stem without accents - a number as it
  // is. The stems of a request are kept: a word met again is not stemmed again.

  function padSearchTerm ( $word, $language ) {

    static $memo = [];

    $key = $language ['code'];

    if ( isset ( $memo [$key] [$word] ) )
      return $memo [$key] [$word];

    if ( isset ( $language ['stop'] [$word] ) )
      $term = '';
    elseif ( preg_match ( '/\p{N}/u', $word ) )
      $term = padSearchFold ( $word );
    else
      $term = padSearchFold ( ( $language ['stem'] ) ( $word ) );

    if ( count ( $memo [$key] ?? [] ) > 50000 )
      $memo [$key] = [];

    return $memo [$key] [$word] = $term;

  }

  // The text of a field: a list's items one after the other, HTML without its tags, its
  // scripts and styles - a block tag parts two words, as for an excerpt - and its entities
  // read.

  function padSearchText ( $value ) {

    if ( is_array ( $value ) )
      return implode ( ' ', array_map ( 'padSearchText', $value ) );

    if ( ! is_scalar ( $value ) or is_bool ( $value ) )
      return '';

    $text = padUnprotect ( (string) $value );

    if ( preg_match ( '#</?[a-z][a-z0-9]*[\s>/]#i', $text ) )
      $text = html_entity_decode ( strip_tags ( preg_replace ( '#<(?=/?(?:p|div|li|h[1-6]|br|hr|tr|td|th|dt|dd|ul|ol|dl|table|caption|pre|blockquote|section|article|aside|header|footer|nav|main|figure|figcaption|address|details|summary)\b)#i', ' <', padExcerptCode ( $text ) ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    return $text;

  }

  // The inverted index of rows: per term the documents with it, per document the fields and
  // the positions of the term there; the length of every field of every document and the
  // average length per field; and the words met, by their unaccented form, to the term each
  // stands for - what a prefix is looked up in.

  function padSearchIndex ( $rows, $fields, $language ) {

    $index = [ 'form' => padSearchForm, 'lang' => $language ['code'], 'fields' => array_column ( $fields, 0 ),
               'n' => 0, 'len' => [], 'avg' => [], 'terms' => [], 'words' => [] ];

    $sums = array_fill ( 0, count ( $fields ), 0 );
    $doc  = 0;

    foreach ( $rows as $row ) {

      $row = padSearchRow ( $row );
      $len = [];

      foreach ( $fields as $f => [ $name ] ) {

        $words = padSearchWords ( padSearchText ( padArrGet ( $row, $name, '' ) ) );
        $len [$f] = 0;

        foreach ( $words as $at => $word ) {
          $term = padSearchTerm ( $word, $language );
          if ( $term === '' )
            continue;
          $len [$f]++;
          $index ['terms'] [$term] [$doc] [$f] [] = $at;
          $index ['words'] [ padSearchFold ( $word ) ] = $term;
        }

        $sums [$f] += $len [$f];

      }

      $index ['len'] [$doc] = $len;
      $doc++;

    }

    $index ['n'] = $doc;

    foreach ( $sums as $f => $sum )
      $index ['avg'] [$f] = $doc ? $sum / $doc : 0;

    ksort ( $index ['words'], SORT_STRING );

    return $index;

  }

  // A row as an array: an object's properties, a scalar as the one field value.

  function padSearchRow ( $row ) {

    if ( is_object ( $row ) )
      return get_object_vars ( $row );

    return is_array ( $row ) ? $row : [ 'value' => $row ];

  }

  // The index of a source, kept in DATA/cache/search/<app>/ under a name made of the source's
  // name, the fields and the language. $sign says what the source is now - the files of a
  // collection, a hash of the rows - and an index kept for another sign is made again by
  // $make, which answers [ rows, index ]. The rows of a collection are kept with the index:
  // reading them means writing every file's Markdown as HTML.

  function padSearchKept ( $name, $fields, $language, $sign, $make, $keepRows ) {

    static $read = [];

    $file = 'cache/search/' . $GLOBALS ['padApp'] . '/' . md5 ( padSearchForm . "\n$name\n" . json_encode ( $fields ) . "\n" . $language ['code'] ) . '.json';

    if ( isset ( $read [$file] ) and $read [$file] ['sign'] === $sign )
      return $read [$file];

    $kept = is_file ( DATA . $file ) ? json_decode ( (string) @file_get_contents ( DATA . $file ), TRUE ) : NULL;

    if ( ! is_array ( $kept ) or ( $kept ['sign'] ?? '' ) !== $sign or ( $kept ['index'] ['form'] ?? 0 ) !== padSearchForm ) {

      [ $rows, $index ] = $make ();

      $kept = [ 'sign' => $sign, 'index' => $index, 'rows' => $keepRows ? array_values ( $rows ) : NULL ];

      $json = json_encode ( $kept, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION );

      if ( $json !== FALSE )
        padFilePut ( $file, $json );

    }

    return $read [$file] = $kept;

  }

  // A query as [ words, prefixes, phrases, out ]: words and prefixes as terms, a phrase as
  // its terms with their places after its first, out as the terms, prefixes and phrases that
  // leave a document out. A word that is a stop word is dropped; so is a phrase of them only.
  // Fifty parts of a thousand characters at the most - a query is typed, not posted.

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
          $term = padSearchTerm ( $word, $language );
          if ( $term === '' )
            continue;
          $first   ??= $at;
          $phrase [] = [ $term, $at - $first ];
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

        foreach ( padSearchWords ( $part [3] ) as $word ) {
          $term = padSearchTerm ( $word, $language );
          if ( $term !== '' )
            $to ['words'] [$term] = TRUE;
        }

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

  // The terms of the words that begin with a prefix, read from the sorted words of the index.

  function padSearchExpand ( $prefix, $index ) {

    $terms = [];

    foreach ( $index ['words'] as $word => $term )
      if ( str_starts_with ( (string) $word, $prefix ) )
        $terms [$term] = TRUE;

    return array_keys ( $terms );

  }

  // The documents of a term - or of a phrase - as [ document => [ field => count ] ].

  function padSearchPostings ( $index, $term ) {

    $docs = [];

    foreach ( $index ['terms'] [$term] ?? [] as $doc => $fields )
      foreach ( $fields as $f => $places )
        $docs [$doc] [$f] = count ( $places );

    return $docs;

  }

  // A phrase is found where each next word stands at its place after the first: the places
  // of the next words are looked up by key, flipped once per field - in_array went through
  // them all for every place of the first word, and a word standing thousands of times in
  // one long field made a phrase take seconds.

  function padSearchPhrase ( $index, $phrase ) {

    $docs = [];

    foreach ( $index ['terms'] [ $phrase [0] [0] ] ?? [] as $doc => $fields )
      foreach ( $fields as $f => $places ) {

        $count = 0;
        $next  = [];

        for ( $i = 1; $i < count ( $phrase ); $i++ )
          $next [$i] = array_flip ( $index ['terms'] [ $phrase [$i] [0] ] [$doc] [$f] ?? [] );

        foreach ( $places as $at ) {
          $whole = TRUE;
          for ( $i = 1; $i < count ( $phrase ) and $whole; $i++ )
            $whole = isset ( $next [$i] [ $at + $phrase [$i] [1] ] );
          if ( $whole )
            $count++;
        }

        if ( $count )
          $docs [$doc] [$f] = $count;

      }

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
        $tf += $boosts [$f] * $count / ( 1 - $b + $b * $index ['len'] [$doc] [$f] / $avg );
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

    foreach ( array_keys ( $query ['words'] ) as $term )
      foreach ( padSearchBm25 ( $index, $boosts, padSearchPostings ( $index, (string) $term ) ) as $doc => $score )
        $scores [$doc] = ( $scores [$doc] ?? 0 ) + $score;

    foreach ( $query ['prefixes'] as $terms ) {
      $best = [];
      foreach ( $terms as $term )
        foreach ( padSearchBm25 ( $index, $boosts, padSearchPostings ( $index, (string) $term ) ) as $doc => $score )
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

    foreach ( array_keys ( $query ['out'] ['words'] ) as $term )
      $not += $index ['terms'] [ (string) $term ] ?? [];

    foreach ( $query ['out'] ['prefixes'] as $terms )
      foreach ( $terms as $term )
        $not += $index ['terms'] [ (string) $term ] ?? [];

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

    $terms = $query ['words'];

    foreach ( $query ['prefixes'] as $list )
      $terms += array_fill_keys ( $list, TRUE );

    foreach ( $query ['phrases'] as $phrase )
      foreach ( $phrase as [ $term ] )
        $terms [$term] = TRUE;

    $words = [];

    foreach ( padSearchWords ( $text ) as $word )
      if ( isset ( $terms [ padSearchTerm ( $word, $language ) ] ) ) {
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
  // every text field of the first row) ]; the options are fields, lang, words (of the
  // excerpt, 30) and excerpt (the field it is cut from, the last of fields). An error is
  // answered as a string.

  function padSearchRun ( $source, $q, $options ) {

    [ $name, $sign, $rows, $keepRows, $fieldsDefault ] = $source + [ 4 => '' ];

    $language = padSearchLanguage ( $options ['lang'] ?? padSearchLocale () );

    if ( is_string ( $language ) )
      return $language;

    if ( ( $options ['fields'] ?? '' ) !== '' or $fieldsDefault !== '' )
      $fields = padSearchFields ( ( $options ['fields'] ?? '' ) !== '' ? $options ['fields'] : $fieldsDefault );
    else {
      if ( is_callable ( $rows ) )
        $rows = $rows ();
      $fields = padSearchGuessFields ( $rows );
    }

    if ( is_string ( $fields ) )
      return $fields;

    $words = $options ['words'] ?? 30;

    if ( ! ctype_digit ( (string) $words ) or (int) $words < 1 )
      return "words= is the length of the excerpt, a number of words - not '" . padMakeSafe ( $words, 20 ) . "'";

    $excerpt = (string) ( $options ['excerpt'] ?? end ( $fields ) [0] ?? '' );

    $kept = padSearchKept ( $name, $fields, $language, $sign, function () use ( &$rows, $fields, $language ) {
      if ( is_callable ( $rows ) )
        $rows = $rows ();
      return [ $rows, padSearchIndex ( $rows, $fields, $language ) ];
    }, $keepRows );

    $index  = $kept ['index'];
    $query  = padSearchQuery ( $q, $language, $index );
    $scores = padSearchScores ( $index, array_column ( $fields, 1 ), $query );

    if ( ! $scores )
      return [];

    if ( $keepRows )
      $rows = $kept ['rows'];
    elseif ( is_callable ( $rows ) )
      $rows = $rows ();

    $rows  = array_values ( (array) $rows );
    $found = [];

    foreach ( $scores as $doc => $score ) {

      $row  = padSearchRow ( $rows [$doc] ?? [] );
      $text = padSearchText ( padArrGet ( $row, $excerpt, '' ) );

      $row ['score']   = round ( $score, 3 );
      $row ['excerpt'] = padExcerpt ( $text, (int) $words, padSearchMatched ( $text, $language, $query ), FALSE, '…' );

      $found [] = $row;

    }

    return $found;

  }

  // The source of {search 'name'}: the Markdown collection of the name (_content/<name>/),
  // else a {data} store, a page's array, a field or a _data file of the name - as data=
  // finds one for {chart}. [ name, sign, rows, keep the rows, the fields when none are
  // given ] - or an error message. A collection is searched in title^2, body by default.

  function padSearchSource ( $name ) {

    global $padDataStore, $pqStore;

    $name = trim ( (string) $name );

    if ( $name === '' )
      return "the search needs a source - {search 'blog'} searches _content/blog/, a data store, a page's array or a _data file of the name";

    if ( $dir = padCollectionDir ( $name ) ) {

      $files = glob ( $dir . '*.md' ) ?: [];
      sort ( $files );

      $sign = [];
      foreach ( $files as $file )
        $sign [] = basename ( $file ) . ' ' . @filemtime ( $file ) . ' ' . @filesize ( $file );

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

    return [ "rows:$name", padSearchSign ( $rows ), $rows, FALSE, '' ];

  }

  // The hits as the single {search/} writes them: a list, each item the title field - a link
  // when link= names a field with one that goes nowhere a script runs - and the excerpt.

  function padSearchList ( $rows, $title, $link ) {

    include_once PAD . 'tags/_lib/calendar.php';

    $h    = fn ( $t ) => htmlspecialchars ( padUnprotectQuotes ( (string) $t ), ENT_QUOTES, 'UTF-8' );
    $html = padSearchStyle () . '<ol class="pad-search">';

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

  // The sign of rows: a hash of them, as JSON - every value, in its order.

  function padSearchSign ( $rows ) {

    return md5 ( (string) json_encode ( $rows, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE ) );

  }

?>
