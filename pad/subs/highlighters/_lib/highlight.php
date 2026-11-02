<?php

  // Language definitions are trusted local type files; escaping belongs to the shared engine.
  function padHighlightLanguage ( $lang ) {
    $lang = strtolower ( trim ( (string) $lang ) );
    if ( padKind ( 'highlighters', $lang ) ) return $lang;
    foreach ( padKinds ( 'highlighters' ) as $kind )
      if ( in_array ( $lang, padHighlighter ( $kind ) ['aliases'], TRUE ) ) return $kind;
    return $lang;
  }

  function padHighlighter ( $kind ) {
    static $definitions = [];
    if ( ! padKind ( 'highlighters', $kind ) ) return [ 'aliases' => [], 'rules' => [] ];
    return $definitions [$kind] ??= include PAD . "subs/highlighters/types/$kind.php";
  }

  function padHighlightRules ( $lang ) {
    return padHighlighter ( padHighlightLanguage ( $lang ) ) ['rules'];
  }

  // PHP is cut by PHP's own tokenizer, each token a class by its kind - flat spans, never
  // one inside another. A text without an opening tag is read as code anyway - the snippet
  // of a function body.

  function padHighlightPhp ( $text ) {

    $open   = ! preg_match ( '/<\?(php|=)/i', $text );
    $tokens = token_get_all ( $open ? "<?php $text" : $text );
    $html   = '';

    if ( $open )
      array_shift ( $tokens );

    foreach ( $tokens as $i => $token ) {

      if ( ! is_array ( $token ) ) {
        $html .= '<span class="hl-pun">' . padHighlightEscape ( $token ) . '</span>';
        continue;
      }

      list ( $id, $part ) = $token;

      $next = '';
      for ( $j = $i + 1; isset ( $tokens [$j] ); $j++ )
        if ( ! is_array ( $tokens [$j] ) or $tokens [$j] [0] != T_WHITESPACE ) {
          $next = is_array ( $tokens [$j] ) ? $tokens [$j] [1] : $tokens [$j];
          break;
        }

      switch ( TRUE ) {
        case $id == T_WHITESPACE:                                              $class = '';    break;
        case in_array ( $id, [ T_COMMENT, T_DOC_COMMENT ] ):                    $class = 'com'; break;
        case in_array ( $id, [ T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE, T_START_HEREDOC, T_END_HEREDOC ] ): $class = 'str'; break;
        case $id == T_VARIABLE:                                                $class = 'var'; break;
        case in_array ( $id, [ T_LNUMBER, T_DNUMBER ] ):                       $class = 'num'; break;
        case in_array ( $id, [ T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLOSE_TAG ] ): $class = 'tag'; break;
        case $id == T_INLINE_HTML:                                             $class = 'htm'; break;
        case $id == T_STRING and in_array ( strtolower ( $part ), [ 'true', 'false', 'null' ] ): $class = 'lit'; break;
        case $id == T_STRING and $next == '(':                                 $class = 'fn';  break;
        case $id == T_STRING:                                                  $class = 'def'; break;
        case ctype_alpha ( str_replace ( '_', '', $part ) ):                   $class = 'kwd'; break;
        default:                                                               $class = 'pun';
      }

      // The opening tag carries the line break after it: kept outside the span, so no
      // line starts inside one.

      $tail = ( $class == 'tag' ) ? substr ( $part, strlen ( rtrim ( $part ) ) ) : '';
      $part = substr ( $part, 0, strlen ( $part ) - strlen ( $tail ) );

      $html .= ( $class === '' ) ? padHighlightEscape ( $part ) : "<span class=\"hl-$class\">" . padHighlightEscape ( $part ) . '</span>' . $tail;

    }

    return $html;

  }

  function padHighlightEscape ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  function padHighlightMarked ( $mark ) {

    $marked = [];

    foreach ( explode ( ',', (string) $mark ) as $part )
      if ( preg_match ( '/^\s*(\d+)\s*(?:-\s*(\d+))?\s*$/', $part, $m ) )
        for ( $i = (int) $m [1]; $i <= (int) ( $m [2] ?? $m [1] ) and $i - $m [1] < 10000; $i++ )
          $marked [$i] = TRUE;

    return $marked;

  }

?>
