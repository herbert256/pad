<?php

  // The body of {search}: the rows of a source a query finds, the best first - the search
  // subsystem, subs/search/_lib/search.php. The first parameter names the source: a Markdown
  // collection (_content/<name>/), else a data store, a page's array (a db() result) or a
  // _data file of the name. q= is the query - words, "a phrase", prefix*, -left out;
  // fields= the fields searched with their boosts ('title^2, body'; a collection's title^2,
  // body, else every text field); lang= the language of its stop words and stemmer (en, nl,
  // de, fr, es, it, pt, sv, da, no, ru - nb and nn are no - the locale's language, else en);
  // words= the length of the excerpt (30) and excerpt= the field it is cut from (the last of
  // fields).
  //
  // As a pair the rows - the source's own fields with score and excerpt added (HTML, print
  // it with {!excerpt}) - and the handling options work on them, first=10 for the ten best;
  // no row is the @else@ - an excerpt is made for the rows they can show only. As a single
  // tag a list of them - the first= best: the first field of fields= as the title, a link
  // to the field link= names, and the excerpt.
  //
  //   {search 'blog', q=$q, fields='title^2, body', lang='en'}
  //     <a href="?blog/post&slug={$slug}">{$title}</a> {$score} {!excerpt}
  //   @else@
  //     Nothing found.
  //   {/search}

  include_once PAD . 'subs/search/_lib/search.php';

  $padSearchSource = padSearchSource ( padUnprotect ( (string) $padParm ) );

  if ( is_string ( $padSearchSource ) ) {
    if ( $padCheckSyntax )
      padError ( "{search}: $padSearchSource" );
    return [];
  }

  $padSearchOptions = [ 'lang'   => padUnprotect ( (string) padTagParm ( 'lang', padSearchLocale () ) ),
                        'fields' => padUnprotect ( (string) padTagParm ( 'fields', '' ) ),
                        'words'  => padTagParm ( 'words', 30 ) ];

  if ( ( $padSearchExcerpt = padTagParm ( 'excerpt', NULL ) ) !== NULL )
    $padSearchOptions ['excerpt'] = padUnprotect ( (string) $padSearchExcerpt );

  // An excerpt is made for the rows the page can show: as a pair, the rows the handling
  // options keep when they keep the first only - first=10 - and as a single tag, whose
  // list the handling options never reached, the rows of first=.

  if ( $padPair [$pad] )
    $padSearchOptions ['excerpts'] = padSearchShown ( $padPrm [$pad] );
  elseif ( isset ( $padPrm [$pad] ['first'] ) )
    $padSearchOptions ['first'] = padSearchShown ( [ 'first' => $padPrm [$pad] ['first'] ] );

  // The language used goes on the xref record, which the reference application shows - nb
  // and nn as the no they search in.

  if ( $padInfoXref ?? FALSE ) {
    $padSearchLang = strtolower ( trim ( $padSearchOptions ['lang'] ) );
    padInfoXref ( 'search', padSearchAlias [$padSearchLang] ?? $padSearchLang );
  }

  $padSearchRows = padSearchRun ( $padSearchSource, (string) padTagParm ( 'q', '' ), $padSearchOptions );

  if ( is_string ( $padSearchRows ) ) {
    if ( $padCheckSyntax )
      padError ( "{search}: $padSearchRows" );
    return [];
  }

  // The style of the excerpts' marks once, before the first row shown, when the pair's
  // template prints the excerpt - a value, so its braces stay text. It went with the
  // excerpt of the best row, and a page that sorted the rows, cut them or printed no
  // excerpt lost it, for every {excerpt} after it as well; the page's own once is left to
  // the {excerpt} tags, which then write it too.

  if ( $padPair [$pad] ) {
    if ( $padSearchRows and str_contains ( $padContent, 'excerpt' ) )
      $padContent = '{first}' . padProtect ( padSearchExcerptStyle () ) . '{/first}' . $padContent;
    return $padSearchRows;
  }

  // The title of a hit in the list: the first field searched.

  if ( $padSearchOptions ['fields'] !== '' )
    $padSearchTitle = padSearchFields ( $padSearchOptions ['fields'] ) [0] [0];
  elseif ( $padSearchSource [4] !== '' )
    $padSearchTitle = padSearchFields ( $padSearchSource [4] ) [0] [0];
  else
    $padSearchTitle = padSearchGuessFields ( $padSearchSource [2] ) [0] [0] ?? '';

  return padSearchList ( $padSearchRows, $padSearchTitle, padUnprotect ( (string) padTagParm ( 'link', '' ) ) );

?>
