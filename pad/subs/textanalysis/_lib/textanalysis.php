<?php
  function padTextAnalysisTokens ( $text, $options ) {
    if ( ! is_string ( $text ) || strlen ( $text ) > 1048576 || ! mb_check_encoding ( $text, 'UTF-8' ) )
      throw new InvalidArgumentException ( 'each document must be valid UTF-8 text of at most 1 MiB' );
    $normalized = ( $options ['normalization'] ?? 'nfc' ) === 'nfc' ? Normalizer::normalize ( $text, Normalizer::FORM_C ) : $text;
    if ( $normalized === FALSE ) throw new InvalidArgumentException ( 'Unicode normalization failed' );
    $tokenText = ( $options ['caseFold'] ?? TRUE ) ? mb_convert_case ( $normalized, MB_CASE_FOLD, 'UTF-8' ) : $normalized;
    if ( preg_match_all ( "/[\p{L}\p{N}][\p{L}\p{N}\p{M}]*(?:['’][\p{L}\p{N}][\p{L}\p{N}\p{M}]*)*/u", $tokenText, $matches ) === FALSE )
      throw new InvalidArgumentException ( 'tokenization failed' );
    if ( count ( $matches [0] ) > 100000 ) throw new InvalidArgumentException ( 'document exceeds 100000 tokens' );
    return [ $normalized, $matches [0] ];
  }

  function padTextAnalysisCounts ( $text, $options ) {
    $rate = $options ['wordsPerMinute'] ?? 200;
    if ( ( ! is_int ( $rate ) && ! is_float ( $rate ) ) || ! is_finite ( (float) $rate ) || $rate <= 0 || $rate > 10000 )
      throw new InvalidArgumentException ( 'wordsPerMinute must be a finite native number in (0,10000]' );
    [ $normalized, $tokens ] = padTextAnalysisTokens ( $text, $options );
    $minutes = count ( $tokens ) / $rate;
    if ( ! is_finite ( $minutes ) ) throw new InvalidArgumentException ( 'estimated reading time exceeds the finite numeric range' );
    $paragraphs = preg_split ( '/\R\h*\R(?:\h*\R)*/u', trim ( $normalized ), -1, PREG_SPLIT_NO_EMPTY );
    $graphemes = preg_match_all ( '/\X/u', $normalized );
    return [ 'bytes' => strlen ( $normalized ), 'codepoints' => mb_strlen ( $normalized, 'UTF-8' ), 'graphemes' => $graphemes,
      'tokens' => count ( $tokens ), 'uniqueTokens' => count ( array_unique ( $tokens ) ), 'paragraphs' => count ( $paragraphs ),
      'wordsPerMinute' => $rate, 'estimatedReadingMinutes' => $minutes ];
  }

  function padTextAnalysisNgrams ( $text, $options ) {
    $n = $options ['n'] ?? 2; $limit = $options ['limit'] ?? 100;
    if ( ! is_int ( $n ) || $n < 1 || $n > 5 || ! is_int ( $limit ) || $limit < 1 || $limit > 10000 )
      throw new InvalidArgumentException ( 'n must be 1..5 and limit must be 1..10000' );
    [ , $tokens ] = padTextAnalysisTokens ( $text, $options ); $frequencies = []; $windows = max ( 0, count ( $tokens ) - $n + 1 );
    for ( $i = 0; $i < $windows; $i++ ) { $key = implode ( ' ', array_slice ( $tokens, $i, $n ) ); $frequencies [$key] = ( $frequencies [$key] ?? 0 ) + 1; }
    $grams = []; foreach ( $frequencies as $gram => $count ) $grams [] = [ 'ngram' => (string) $gram, 'count' => $count ];
    usort ( $grams, fn ( $a, $b ) => ( $b ['count'] <=> $a ['count'] ) ?: strcmp ( $a ['ngram'], $b ['ngram'] ) );
    return [ 'n' => $n, 'windows' => $windows, 'unique' => count ( $grams ), 'ngrams' => array_slice ( $grams, 0, $limit ) ];
  }

  function padTextAnalysisSimilarity ( $rows, $options ) {
    $metric = $options ['metric'] ?? 'jaccard'; $threshold = $options ['threshold'] ?? 0;
    if ( ! in_array ( $metric, [ 'jaccard', 'cosine' ], TRUE ) || ( ! is_int ( $threshold ) && ! is_float ( $threshold ) ) || ! is_finite ( (float) $threshold ) || $threshold < 0 || $threshold > 1 )
      throw new InvalidArgumentException ( 'metric is jaccard or cosine; threshold is a finite number in [0,1]' );
    if ( ! is_array ( $rows ) || count ( $rows ) > 200 ) throw new InvalidArgumentException ( 'similarity requires at most 200 identity-keyed document strings' );
    $documents = []; $bytes = $tokensTotal = 0;
    foreach ( $rows as $id => $text ) {
      if ( ! is_string ( $text ) || ( $bytes += strlen ( $text ) ) > 4194304 ) throw new InvalidArgumentException ( 'corpus exceeds 4 MiB or contains non-string text' );
      [ , $tokens ] = padTextAnalysisTokens ( $text, $options ); $tokensTotal += count ( $tokens );
      if ( $tokensTotal > 100000 ) throw new InvalidArgumentException ( 'corpus exceeds 100000 tokens' );
      $frequencies = array_count_values ( $tokens ); $norm = 0.0;
      foreach ( $frequencies as $count ) $norm += $count * $count;
      $documents [] = [ 'id' => $id, 'counts' => $frequencies, 'norm' => sqrt ( $norm ) ];
    }
    $work = 0; $pairs = [];
    for ( $i = 0; $i < count ( $documents ); $i++ ) for ( $j = $i + 1; $j < count ( $documents ); $j++ ) {
      $a = $documents [$i]; $b = $documents [$j]; $small = $a ['counts']; $large = $b ['counts'];
      if ( count ( $small ) > count ( $large ) ) [ $small, $large ] = [ $large, $small ];
      $work += count ( $small );
      if ( $work > 5000000 ) throw new InvalidArgumentException ( 'pairwise comparison exceeds 5000000 token lookups' );
      $intersection = $dot = 0;
      foreach ( $small as $token => $count ) if ( isset ( $large [$token] ) ) { $intersection++; $dot += $count * $large [$token]; }
      $denominator = $metric === 'jaccard' ? count ( $small ) + count ( $large ) - $intersection : $a ['norm'] * $b ['norm'];
      $score = $denominator == 0 ? ( ! $small && ! $large ? 1.0 : 0.0 ) : ( $metric === 'jaccard' ? $intersection : $dot ) / $denominator;
      $score = max ( 0.0, min ( 1.0, $score ) );
      if ( $score >= $threshold ) $pairs [] = [ 'a' => $a ['id'], 'b' => $b ['id'], 'score' => $score ];
    }
    // Stable sort retains input pair order on equal scores.
    usort ( $pairs, fn ( $a, $b ) => $b ['score'] <=> $a ['score'] );
    return [ 'metric' => $metric, 'documents' => count ( $documents ), 'comparisons' => intdiv ( count ( $documents ) * ( count ( $documents ) - 1 ), 2 ), 'pairs' => $pairs ];
  }
?>
