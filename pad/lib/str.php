<?php

  // String helpers for a page's PHP: what Laravel's Str and Symfony's String component give
  // a controller, written the PAD way - plain functions over UTF-8 text that count in
  // characters (mb_*), never in bytes, so an accented letter or an emoji is one character.
  //
  // padStrSlug        a readable URL part - the slug pipe calls it, so both answer the same
  // padStrLimit       at most so many characters, $end added when something was cut
  // padStrWords       at most so many words, $end added when something was cut
  // padStrExcerpt     the phrase with so many characters on either side, case-insensitive
  // padStrSquish      trimmed, and every run of whitespace - unicode spaces too - one space
  // padStrCamel       userName
  // padStrStudly      UserName
  // padStrSnake       user_name, or with another delimiter
  // padStrKebab       user-name
  // padStrHeadline    User Name - from snake, kebab, camel or studly case
  // padStrTitle       Hello World - every word with a capital
  // padStrAfter       what follows the first occurrence - the after pipe calls it
  // padStrAfterLast   what follows the last occurrence - the afterLast pipe calls it
  // padStrBefore      what precedes the first occurrence - the before pipe calls it
  // padStrBeforeLast  what precedes the last occurrence - the beforeLast pipe calls it
  // padStrBetween     what stands between the first $from and the last $to
  // padStrIs          whether a value matches a pattern with * wildcards, or one of a list
  // padStrMask        a stretch of the text replaced by one character - a card number
  // padStrRandom      a random string of letters and digits, from random_int
  // padStrUuid        a version 4 (random) or version 7 (time-ordered) UUID
  // padStrPlural      the English plural of a word - kept as it is for a count of one
  // padStrSingular    the English singular of a word
  //
  // A text is anything PHP turns into a string: NULL and FALSE are '', a number its digits,
  // an object with __toString its string. An array or another object is a mistake of the
  // author, named with padError, after which the function goes on with ''. Text that is not
  // valid UTF-8 has its broken bytes replaced by '?' first, so the /u expressions below never
  // fail on it - except in the functions the pipes share, which answer what they always did.
  //
  // Before these a page's PHP had only PHP's own byte functions, or the pipes of a template:
  // a title shortened in PHP with substr could cut a character in half.

  // The spaces that show as a space - PHP's \s, the unicode separators (no-break space, em
  // space, ideographic space ...) and the Hangul fillers - and, for trimming the ends only,
  // the invisible ones: the byte order mark, the zero width space, the left-to-right mark.
  // A zero width space inside a word is left alone: it marks where a long word may break.

  const padStrSpaces = '\s\p{Z}\x{3164}\x{1160}';
  const padStrTrims  = padStrSpaces . '\x{FEFF}\x{200B}\x{200E}';

  // padStrSlug: 'Crème Brûlée & Co.' becomes 'creme-brulee-co'. Accented letters are
  // transliterated to plain ASCII (intl's transliterator when PHP has it, iconv otherwise),
  // the rest of anything that is not a letter or digit becomes one separator, and the result
  // is lower case with no separator at either end. The slug pipe is this function, so a
  // slug made in PHP for a database column is the one a template makes for a link.

  function padStrSlug ( $text, $separator = '-' ) {

    $text      = padStrText ( $text,      'padStrSlug', FALSE );
    $separator = padStrText ( $separator, 'padStrSlug', FALSE );

    if ( function_exists ( 'transliterator_transliterate' ) )
      $text = transliterator_transliterate ( 'Any-Latin; Latin-ASCII', $text ) ?: $text;
    elseif ( function_exists ( 'iconv' ) )
      $text = @iconv ( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $text ) ?: $text;

    // The runs are cut to one space and trimmed before the separator goes in, so it is used
    // as it is written: handed to preg_replace as the replacement, slug('$0') put each run
    // back where it was, and handed to trim as a list of characters, slug('..') ended the
    // request on an invalid range and slug('x') ate an x at either end of the text itself.

    $text = strtolower ( $text );
    $text = trim ( preg_replace ( '/[^a-z0-9]+/', ' ', $text ) );

    return str_replace ( ' ', $separator, $text );

  }

  // padStrLimit: at most $limit characters of the text. When something was cut, the space
  // the cut left at the end is trimmed and $end follows - 'The quick brown fox' at 10 is
  // 'The quick...', not 'The quick ...'; a text that fits comes back as it is, without $end.
  // $end is not counted in the limit.

  function padStrLimit ( $text, $limit = 100, $end = '...' ) {

    $text  = padStrText  ( $text, 'padStrLimit' );
    $end   = padStrText  ( $end,  'padStrLimit' );
    $limit = padStrWhole ( $limit, 'padStrLimit', 'limit' );

    if ( $limit === NULL )
      return '';

    if ( mb_strlen ( $text ) <= $limit )
      return $text;

    return padStrTrimEnd ( mb_substr ( $text, 0, $limit ) ) . $end;

  }

  // padStrWords: the first $words words, a word being a run of anything but whitespace. As
  // with padStrLimit, $end follows only when words were left out. The words are found one by
  // one rather than with a counted regular expression, which PCRE refuses above 65535.

  function padStrWords ( $text, $words = 100, $end = '...' ) {

    $text  = padStrText  ( $text, 'padStrWords' );
    $end   = padStrText  ( $end,  'padStrWords' );
    $words = padStrWhole ( $words, 'padStrWords', 'number of words' );

    if ( $words === NULL )
      return '';

    preg_match_all ( '/[^' . padStrSpaces . ']+/u', $text, $found, PREG_OFFSET_CAPTURE );

    if ( count ( $found [0] ) <= $words )
      return $text;

    if ( $words == 0 )
      return $end;

    [ $last, $at ] = $found [0] [ $words - 1 ];

    return substr ( $text, 0, $at + strlen ( $last ) ) . $end;

  }

  // padStrExcerpt: the first place the phrase stands - found case-insensitively, shown as
  // the text has it - with up to $radius characters on either side, $omission marking a side
  // that was cut. A search result shows where the word was found, not the first lines of the
  // text. A phrase that is not there answers ''; an empty phrase is found at the start.

  function padStrExcerpt ( $text, $phrase, $radius = 100, $omission = '...' ) {

    $text     = padStrText  ( $text,     'padStrExcerpt' );
    $phrase   = padStrText  ( $phrase,   'padStrExcerpt' );
    $omission = padStrText  ( $omission, 'padStrExcerpt' );
    $radius   = padStrWhole ( $radius,   'padStrExcerpt', 'radius' );

    if ( $radius === NULL )
      return '';

    // The phrase alone is searched, and what stands before and after it is cut by its
    // offset: ^(.*?)phrase(.*)$ took a backtracking step for every character before the
    // phrase, and past PCRE's limit of a million a phrase that is there answered ''.

    if ( ! preg_match ( '/' . preg_quote ( $phrase, '/' ) . '/iu', $text, $found, PREG_OFFSET_CAPTURE ) )
      return '';

    [ $phrase, $at ] = $found [0];

    $match = [ 1 => substr ( $text, 0, $at ), 2 => $phrase, 3 => substr ( $text, $at + strlen ( $phrase ) ) ];

    $before = padStrTrimStart ( $match [1] );
    $start  = padStrTrimStart ( mb_substr ( $before, max ( mb_strlen ( $before ) - $radius, 0 ) ) );

    if ( $start !== $before )
      $start = $omission . $start;

    $after = padStrTrimEnd ( $match [3] );
    $end   = padStrTrimEnd ( mb_substr ( $after, 0, $radius ) );

    if ( $end !== $after )
      $end .= $omission;

    return $start . $match [2] . $end;

  }

  // padStrSquish: the text trimmed at both ends, and every run of whitespace inside it -
  // tabs, newlines, no-break and other unicode spaces - one plain space. Text pasted into a
  // form field, or put together from several pieces, compares and stores the same way.

  function padStrSquish ( $text ) {

    $text = padStrText ( $text, 'padStrSquish' );
    $text = padStrTrimEnd ( padStrTrimStart ( $text ) );

    return preg_replace ( '/[' . padStrSpaces . ']+/u', ' ', $text );

  }

  // The case functions read the words of the text the same way, whatever case it is in: a
  // run of letters and digits is a word, anything else between them separates - an
  // underscore, a dash, a space, a dot - and so does a change of case: userName is user and
  // Name, HTMLParser is HTML and Parser, address2Line is address2 and Line. Each word is then
  // written in the case asked for, so the functions turn into each other in every direction,
  // and an acronym reads as a word: XMLHttpRequest becomes xml_http_request and XmlHttpRequest.

  // padStrCamel: userName - the first word lower case, every next word with a capital.

  function padStrCamel ( $text ) {

    $out = '';

    foreach ( padStrWordList ( padStrText ( $text, 'padStrCamel' ) ) as $index => $word )
      $out .= $index ? padStrUpperFirst ( mb_strtolower ( $word ) ) : mb_strtolower ( $word );

    return $out;

  }

  // padStrStudly: UserName - every word with a capital, nothing between them.

  function padStrStudly ( $text ) {

    $out = '';

    foreach ( padStrWordList ( padStrText ( $text, 'padStrStudly' ) ) as $word )
      $out .= padStrUpperFirst ( mb_strtolower ( $word ) );

    return $out;

  }

  // padStrSnake: user_name - the words in lower case, the delimiter between them.

  function padStrSnake ( $text, $delimiter = '_' ) {

    $words     = padStrWordList ( padStrText ( $text, 'padStrSnake' ) );
    $delimiter = padStrText ( $delimiter, 'padStrSnake' );

    return implode ( $delimiter, array_map ( 'mb_strtolower', $words ) );

  }

  // padStrKebab: user-name - snake case with a dash, the form of a CSS class or a URL part.

  function padStrKebab ( $text ) {

    return padStrSnake ( padStrText ( $text, 'padStrKebab' ), '-' );

  }

  // padStrHeadline: 'User Name' - every word with a capital, a space between them. A column
  // name or a field name becomes a label: created_at is 'Created At', orderTotal 'Order Total'.

  function padStrHeadline ( $text ) {

    $words = padStrWordList ( padStrText ( $text, 'padStrHeadline' ) );

    foreach ( $words as $index => $word )
      $words [$index] = padStrUpperFirst ( mb_strtolower ( $word ) );

    return implode ( ' ', $words );

  }

  // padStrTitle: 'Hello World' - every word of the text with a capital and the rest of it in
  // lower case, the text otherwise as it is: PHP's unicode title case, so 'élan vital' is
  // 'Élan Vital'. Where padStrHeadline makes a label of a name, this one recases a sentence.

  function padStrTitle ( $text ) {

    return mb_convert_case ( padStrText ( $text, 'padStrTitle' ), MB_CASE_TITLE, 'UTF-8' );

  }

  // padStrAfter, padStrAfterLast, padStrBefore and padStrBeforeLast are the pipes after,
  // afterLast, before and beforeLast - those pipes call them, so a page's PHP and its
  // template cut a text the same way. A text that does not contain the search string comes
  // back unchanged, and a multi-character search string is skipped whole. An empty search
  // string is found at the start by the first two and at the end by the last two, as PHP's
  // strpos and strrpos find it. They work on bytes, which is safe for UTF-8: a whole
  // character found is always a whole character cut.

  function padStrAfter ( $text, $search ) {

    $text   = padStrText ( $text,   'padStrAfter', FALSE );
    $search = padStrText ( $search, 'padStrAfter', FALSE );
    $at     = strpos ( $text, $search );

    if ( $at === FALSE )
      return $text;

    return substr ( $text, $at + strlen ( $search ) );

  }

  function padStrAfterLast ( $text, $search ) {

    $text   = padStrText ( $text,   'padStrAfterLast', FALSE );
    $search = padStrText ( $search, 'padStrAfterLast', FALSE );
    $at     = strrpos ( $text, $search );

    if ( $at === FALSE )
      return $text;

    return substr ( $text, $at + strlen ( $search ) );

  }

  function padStrBefore ( $text, $search ) {

    $text   = padStrText ( $text,   'padStrBefore', FALSE );
    $search = padStrText ( $search, 'padStrBefore', FALSE );
    $at     = strpos ( $text, $search );

    if ( $at === FALSE )
      return $text;

    return substr ( $text, 0, $at );

  }

  function padStrBeforeLast ( $text, $search ) {

    $text   = padStrText ( $text,   'padStrBeforeLast', FALSE );
    $search = padStrText ( $search, 'padStrBeforeLast', FALSE );
    $at     = strrpos ( $text, $search );

    if ( $at === FALSE )
      return $text;

    return substr ( $text, 0, $at );

  }

  // padStrBetween: what stands between the first $from and the last $to - padStrAfter, then
  // padStrBeforeLast on what it answered, as Laravel's Str::between does: 'This is my name'
  // between 'This' and 'name' is ' is my '. Either string absent, or empty, leaves that side
  // as it is. (The between pipe is something else: a test whether a number lies between two
  // others.) The first $to after $from is padStrBefore ( padStrAfter ( $text, $from ), $to ).

  function padStrBetween ( $text, $from, $to ) {

    $text = padStrText ( $text, 'padStrBetween', FALSE );
    $from = padStrText ( $from, 'padStrBetween', FALSE );
    $to   = padStrText ( $to,   'padStrBetween', FALSE );

    return padStrBeforeLast ( padStrAfter ( $text, $from ), $to );

  }

  // padStrIs: whether the value matches the pattern, a * in it standing for any run of
  // characters, none included - 'admin/*' matches 'admin/users'. The match is exact and case
  // sensitive otherwise, over the whole value. $pattern may be a list: TRUE when any of them
  // matches, FALSE for an empty list. A route, a file name or a permission name checked
  // against a list of allowed shapes without writing a regular expression.

  function padStrIs ( $pattern, $value ) {

    $value = padStrText ( $value, 'padStrIs' );

    foreach ( is_iterable ( $pattern ) ? $pattern : [ $pattern ] as $one ) {

      $one = padStrText ( $one, 'padStrIs' );

      if ( $one === '*' or $one === $value )
        return TRUE;

      $regex = '#^' . str_replace ( '\*', '.*', preg_quote ( $one, '#' ) ) . '\z#su';

      if ( preg_match ( $regex, $value ) === 1 )
        return TRUE;

    }

    return FALSE;

  }

  // padStrMask: the characters from $index on - $length of them, or all to the end - each
  // replaced by $character (its first character when it is longer). A negative $index counts
  // from the end, and a negative $length stops that many characters before the end, as
  // mb_substr reads them: a card number masked with ( $card, '*', 4, -4 ) keeps the first
  // four digits and the last four. An index past the end masks nothing. A wrong argument
  // answers '' - never the text unmasked.

  function padStrMask ( $text, $character, $index, $length = NULL ) {

    $text      = padStrText  ( $text,      'padStrMask' );
    $character = padStrText  ( $character, 'padStrMask' );
    $index     = padStrWhole ( $index,     'padStrMask', 'index', TRUE );

    if ( $index === NULL )
      return '';

    if ( $length !== NULL and ( $length = padStrWhole ( $length, 'padStrMask', 'length', TRUE ) ) === NULL )
      return '';

    if ( $character === '' ) {
      padError ( 'padStrMask needs a character to mask with' );
      return '';
    }

    $segment = mb_substr ( $text, $index, $length );

    if ( $segment === '' )
      return $text;

    $size  = mb_strlen ( $segment );
    $start = $index < 0 ? max ( 0, mb_strlen ( $text ) + $index ) : $index;

    return mb_substr ( $text, 0, $start )
         . str_repeat ( mb_substr ( $character, 0, 1 ), $size )
         . mb_substr ( $text, $start + $size );

  }

  // padStrRandom: $length characters drawn from A-Z, a-z and 0-9 with random_int, PHP's
  // cryptographically secure generator - good for a token, a code to mail, a file name
  // nobody can guess. The engine's own names - session and request ids - come from here
  // too, through padRandomString.

  function padStrRandom ( $length = 16 ) {

    $length = padStrWhole ( $length, 'padStrRandom', 'length' );

    if ( ! $length )
      return '';

    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $out   = '';

    for ( $i = 0; $i < $length; $i++ )
      $out .= $chars [ random_int ( 0, 61 ) ];

    return $out;

  }

  // padStrUuid: a UUID as RFC 9562 writes it, lower case, 8-4-4-4-12 hex digits. Version 4 is
  // 122 random bits. Version 7 starts with the time in milliseconds, so ids made later sort
  // later - a database index on them stays in order, where random ids land all over it;
  // within one millisecond a counter in the next 12 bits keeps the ids of one request in the
  // order they were made (RFC 9562, section 6.2, method 1). Any other version is an error.

  function padStrUuid ( $version = 4 ) {

    if ( ! in_array ( $version, [ 4, 7, '4', '7' ], TRUE ) ) {
      padError ( 'padStrUuid makes a version 4 or a version 7 UUID, not ' . padStrShow ( $version ) );
      return '';
    }

    $bytes = random_bytes ( 16 );

    if ( $version == 7 )
      $bytes = padStrUuidTime () . substr ( $bytes, 8 );
    else
      $bytes [6] = chr ( ( ord ( $bytes [6] ) & 0x0F ) | 0x40 );

    $bytes [8] = chr ( ( ord ( $bytes [8] ) & 0x3F ) | 0x80 );

    $hex = bin2hex ( $bytes );

    return substr ( $hex,  0,  8 ) . '-' . substr ( $hex,  8, 4 ) . '-' . substr ( $hex, 12, 4 )
         . '-' . substr ( $hex, 16, 4 ) . '-' . substr ( $hex, 20 );

  }

  // The first eight bytes of a version 7 UUID: 48 bits of Unix time in milliseconds, the
  // version, and a 12-bit counter. The counter starts at a random point in its lower half on
  // every new millisecond and counts up within one; when it runs out, or the clock went
  // back, the time is carried one millisecond on, so no id of this request sorts before an
  // earlier one.

  function padStrUuidTime () {

    static $last = 0, $count = 0;

    [ $micro, $seconds ] = explode ( ' ', microtime () );

    $now = (int) $seconds * 1000 + (int) ( (float) $micro * 1000 );

    if ( $now > $last ) {
      $last  = $now;
      $count = random_int ( 0, 0x7FF );
    } elseif ( ++$count > 0xFFF ) {
      $last++;
      $count = random_int ( 0, 0x7FF );
    }

    return substr ( pack ( 'J', $last ), 2 ) . chr ( 0x70 | ( $count >> 8 ) ) . chr ( $count & 0xFF );

  }

  // padStrPlural: the English plural of the last word of the text - 'item' 'items', 'city'
  // 'cities', 'person' 'people', 'blog post' 'blog posts', 'salesPerson' 'salesPeople'. A
  // count of 1 or -1 keeps the word as it is, so "$n " . padStrPlural ( 'file', $n ) reads
  // right for every $n; the count may be an array or a Countable, whose size is used. The
  // word keeps its case: Person is People, PERSON is PEOPLE. A word already plural, and an
  // uncountable one (sheep, information, equipment ...), stays as it is; a text that does not
  // end in a letter has no word to change.

  function padStrPlural ( $word, $count = 2 ) {

    $word = padStrText ( $word, 'padStrPlural' );

    if ( is_array ( $count ) or $count instanceof Countable )
      $count = count ( $count );

    if ( ! is_int ( $count ) and ! is_float ( $count ) and ! ( is_string ( $count ) and is_numeric ( $count ) ) ) {
      padError ( 'padStrPlural needs a number as its count, not ' . padStrShow ( $count ) );
      return $word;
    }

    if ( abs ( (float) $count ) == 1 )
      return $word;

    return padStrInflect ( $word, TRUE );

  }

  // padStrSingular: the English singular of the last word of the text - 'items' 'item',
  // 'cities' 'city', 'people' 'person', 'knives' 'knife', 'analyses' 'analysis'. A word
  // already singular, and an uncountable one, stays as it is; the case is kept.

  function padStrSingular ( $word ) {

    return padStrInflect ( padStrText ( $word, 'padStrSingular' ), FALSE );

  }

  // The inflection both share. The last run of letters is the word; it is changed in lower
  // case, and only what changed at its end is put back: the part both forms share keeps the
  // letters of the text as they were (salesPerson keeps 'salesPe' and gets 'ople'), and what
  // is added is in capitals when the word was all capitals. A word that starts with a capital
  // and has nothing of it left gets one again.

  function padStrInflect ( $text, $plural ) {

    if ( ! preg_match ( '/^(.*?)(\p{L}[\p{L}\p{M}]*)([' . padStrTrims . ']*)\z/su', $text, $match ) )
      return $text;

    [ , $head, $word, $tail ] = $match;

    $lower = mb_strtolower ( $word );
    $new   = $plural ? padStrPluralOf ( $lower ) : padStrSingularOf ( $lower );

    if ( $new === $lower )
      return $text;

    $length = mb_strlen ( $word );
    $size   = mb_strlen ( $lower );
    $upper  = ( $length > 1 and $word === mb_strtoupper ( $word ) );
    $keep   = 0;

    while ( $keep < $size and mb_substr ( $lower, $keep, 1 ) === mb_substr ( $new, $keep, 1 ) )
      $keep++;

    // What the change takes off is counted from the end, where it happens: a letter whose
    // lower case is longer than itself (the İ of İstanbul) stands at the start, untouched.

    $kept  = mb_substr ( $word, 0, max ( 0, $length - ( $size - $keep ) ) );
    $added = mb_substr ( $new, $keep );

    if ( $upper )
      $added = mb_strtoupper ( $added );
    elseif ( $kept === '' and mb_substr ( $word, 0, 1 ) !== mb_substr ( $lower, 0, 1 ) )
      $added = padStrUpperFirst ( $added );

    return $head . $kept . $added . $tail;

  }

  // The plural of a word in lower case: uncountable as it is; the regular words that look
  // irregular (human, specimen, olive) past the irregular tables; the irregular words, whole
  // (mouse, criterion, hero) or as the end of a compound (salesperson, grandchild, bookshelf);
  // then the rules: -sis -ses, -s -sh -ch -x -z -es, another -s is taken as plural already,
  // -y after a consonant -ies, anything else -s.

  function padStrPluralOf ( $word ) {

    $tables = padStrInflections ();

    if ( padStrUncountable ( $word, $tables ) )
      return $word;

    if ( ! isset ( $tables ['regular'] [$word] ) ) {

      if ( isset ( $tables ['plural'] [$word] ) )
        return $tables ['plural'] [$word];

      if ( isset ( $tables ['singular'] [$word] ) )
        return $word;

      foreach ( $tables ['endings'] as $one => $many )
        if ( str_ends_with ( $word, $one ) )
          return substr ( $word, 0, -strlen ( $one ) ) . $many;
        elseif ( str_ends_with ( $word, $many ) )
          return $word;

    }

    if ( str_ends_with ( $word, 'sis' ) )
      return substr ( $word, 0, -2 ) . 'es';

    if ( preg_match ( '/(?:ss|sh|ch|x|z|us|is)$/', $word ) )
      return $word . 'es';

    if ( str_ends_with ( $word, 's' ) )
      return $word;

    if ( preg_match ( '/(?:[^aeiou]|qu)y$/', $word ) )
      return substr ( $word, 0, -1 ) . 'ies';

    return $word . 's';

  }

  // The singular of a word in lower case: the same tables read the other way, then the rules
  // undone: -ies -y, -uses -us for the -us words and -use otherwise, -yses and -theses -is,
  // -sses -shes -xes -zzes -tzes and -ches lose -es (but a cache or an avalanche keeps its
  // e), and any other -s goes - gloves glove, shoes shoe, cases case (the f and fe words, and
  // hero and the other -oes, are in the tables). A word not ending in s, or ending in -ss,
  // -us or -is, is taken as singular already.

  function padStrSingularOf ( $word ) {

    $tables = padStrInflections ();

    if ( padStrUncountable ( $word, $tables ) )
      return $word;

    if ( ! isset ( $tables ['regular'] [$word] ) ) {

      if ( isset ( $tables ['singular'] [$word] ) )
        return $tables ['singular'] [$word];

      if ( isset ( $tables ['plural'] [$word] ) )
        return $word;

      foreach ( $tables ['endings'] as $one => $many )
        if ( str_ends_with ( $word, $many ) )
          return substr ( $word, 0, -strlen ( $many ) ) . $one;
        elseif ( str_ends_with ( $word, $one ) )
          return $word;

    }

    if ( ! str_ends_with ( $word, 's' ) or preg_match ( '/(?:ss|us|is)$/', $word ) )
      return $word;

    if ( str_ends_with ( $word, 'ies' ) )
      return substr ( $word, 0, -3 ) . 'y';

    if ( str_ends_with ( $word, 'uses' ) )
      return isset ( $tables ['us'] [ substr ( $word, 0, -2 ) ] ) ? substr ( $word, 0, -2 ) : substr ( $word, 0, -1 );

    if ( preg_match ( '/(?:yses|theses)$/', $word ) )
      return substr ( $word, 0, -2 ) . 'is';

    if ( preg_match ( '/(?:ss|sh|x|zz|tz)es$/', $word ) )
      return substr ( $word, 0, -2 );

    if ( str_ends_with ( $word, 'ches' ) )
      return preg_match ( '/(?:(?<![eo])ache|niche|cliche|quiche|fiche|pastiche|avalanche|tranche|psyche|creche|brioche|cloche)s$/', $word )
           ? substr ( $word, 0, -1 ) : substr ( $word, 0, -2 );

    return substr ( $word, 0, -1 );

  }

  // Whether a word has no plural: one of the uncountable words, or a compound ending in one
  // of those that compound safely (goldfish, metadata, software, userInformation) - 'rice'
  // is matched whole only, or every price would be uncountable.

  function padStrUncountable ( $word, $tables ) {

    if ( isset ( $tables ['uncountable'] [$word] ) )
      return TRUE;

    foreach ( $tables ['uncountableEndings'] as $end )
      if ( str_ends_with ( $word, $end ) )
        return TRUE;

    return FALSE;

  }

  // The tables of the inflection, built once per request: the words that are their own
  // plural, the irregular pairs matched whole (plural: singular => plural, singular: the
  // other way), the irregular endings matched at the end of a compound too, the regular
  // words those endings would catch, and the -us words whose plural is -uses.
  //
  // The nouns in -u and -i whose plural only adds an s - menu, guru, ski, emoji - stand
  // among the pairs, menu and the -eau words among the endings (submenu, mainMenu): the
  // rules read a word in -us or -is as a singular, so 'menus' stayed 'menus' as a singular
  // and became 'menuses' as a plural.

  function padStrInflections () {

    static $tables = NULL;

    if ( $tables !== NULL )
      return $tables;

    $uncountable = [
      'sheep', 'fish', 'deer', 'moose', 'swine', 'bison', 'cattle', 'salmon', 'trout', 'tuna',
      'cod', 'aircraft', 'spacecraft', 'hovercraft', 'series', 'species', 'means', 'news',
      'corps', 'mews', 'gallows', 'scissors', 'trousers', 'jeans', 'pliers', 'headquarters',
      'chassis', 'kudos', 'chaos', 'information', 'equipment', 'money', 'rice', 'data',
      'metadata', 'media', 'multimedia', 'software', 'hardware', 'firmware', 'middleware',
      'advice', 'luggage', 'baggage', 'furniture', 'knowledge', 'traffic', 'feedback',
      'homework', 'music', 'evidence', 'research', 'staff', 'progress', 'weather', 'wheat',
      'wildlife', 'electricity', 'police', 'offspring', 'physics', 'mathematics', 'economics',
      'politics', 'ethics', 'athletics', 'gymnastics', 'linguistics'
    ];

    $uncountableEndings = [
      'fish', 'sheep', 'deer', 'information', 'equipment', 'data', 'media', 'ware', 'money',
      'furniture', 'knowledge', 'luggage', 'baggage', 'feedback', 'research', 'homework',
      'traffic', 'advice', 'evidence', 'music', 'wildlife', 'police', 'offspring', 'series',
      'species'
    ];

    $irregular = [
      'mouse'      => 'mice',       'louse'      => 'lice',       'goose'      => 'geese',
      'foot'       => 'feet',       'tooth'      => 'teeth',      'ox'         => 'oxen',
      'die'        => 'dice',       'quiz'       => 'quizzes',    'fez'        => 'fezzes',
      'whiz'       => 'whizzes',    'passerby'   => 'passersby',  'criterion'  => 'criteria',
      'phenomenon' => 'phenomena',  'cactus'     => 'cacti',      'focus'      => 'foci',
      'fungus'     => 'fungi',      'nucleus'    => 'nuclei',     'radius'     => 'radii',
      'stimulus'   => 'stimuli',    'syllabus'   => 'syllabi',    'alumnus'    => 'alumni',
      'terminus'   => 'termini',    'genus'      => 'genera',     'corpus'     => 'corpora',
      'crisis'     => 'crises',     'oasis'      => 'oases',      'axis'       => 'axes',
      'diagnosis'  => 'diagnoses',  'prognosis'  => 'prognoses',  'emphasis'   => 'emphases',
      'index'      => 'indices',    'matrix'     => 'matrices',   'vertex'     => 'vertices',
      'appendix'   => 'appendices', 'curriculum' => 'curricula',  'memorandum' => 'memoranda',
      'bacterium'  => 'bacteria',   'gas'        => 'gases',      'lens'       => 'lenses',
      'atlas'      => 'atlases',    'canvas'     => 'canvases',   'alias'      => 'aliases',
      'bias'       => 'biases',     'iris'       => 'irises',     'stomach'    => 'stomachs',
      'monarch'    => 'monarchs',   'epoch'      => 'epochs',     'patriarch'  => 'patriarchs',
      'matriarch'  => 'matriarchs', 'oligarch'   => 'oligarchs',  'eunuch'     => 'eunuchs',
      'loch'       => 'lochs',      'tech'       => 'techs',      'hero'       => 'heroes',
      'potato'     => 'potatoes',   'tomato'     => 'tomatoes',   'echo'       => 'echoes',
      'veto'       => 'vetoes',     'torpedo'    => 'torpedoes',  'embargo'    => 'embargoes',
      'buffalo'    => 'buffaloes',  'domino'     => 'dominoes',   'mosquito'   => 'mosquitoes',
      'tornado'    => 'tornadoes',  'volcano'    => 'volcanoes',  'cargo'      => 'cargoes',
      'movie'      => 'movies',     'cookie'     => 'cookies',    'zombie'     => 'zombies',
      'pie'        => 'pies',       'tie'        => 'ties',       'lie'        => 'lies',
      'calorie'    => 'calories',   'rookie'     => 'rookies',    'genie'      => 'genies',
      'hippie'     => 'hippies',    'brownie'    => 'brownies',   'prairie'    => 'prairies',
      'selfie'     => 'selfies',    'hoodie'     => 'hoodies',    'freebie'    => 'freebies',
      'goalie'     => 'goalies',    'newbie'     => 'newbies',    'smoothie'   => 'smoothies',
      'sortie'     => 'sorties',    'auntie'     => 'aunties',    'birdie'     => 'birdies',
      'budgie'     => 'budgies',    'techie'     => 'techies',    'foodie'     => 'foodies',
      'collie'     => 'collies',    'emu'        => 'emus',       'gnu'        => 'gnus',
      'tutu'       => 'tutus',      'haiku'      => 'haikus',     'tofu'       => 'tofus',
      'sudoku'     => 'sudokus',    'ski'        => 'skis',       'taxi'       => 'taxis',
      'kiwi'       => 'kiwis',      'alibi'      => 'alibis',     'bikini'     => 'bikinis',
      'martini'    => 'martinis',   'safari'     => 'safaris',    'emoji'      => 'emojis',
      'wiki'       => 'wikis',      'sari'       => 'saris',      'deli'       => 'delis',
      'yeti'       => 'yetis',      'chili'      => 'chilis',     'khaki'      => 'khakis',
      'semi'       => 'semis',      'mini'       => 'minis'
    ];

    // Woman before man: both answer the same, but the longer one is the word meant.

    $endings = [
      'person' => 'people',   'child'  => 'children', 'woman'  => 'women',    'man'    => 'men',
      'knife'  => 'knives',   'wife'   => 'wives',    'life'   => 'lives',    'leaf'   => 'leaves',
      'loaf'   => 'loaves',   'thief'  => 'thieves',  'sheaf'  => 'sheaves',  'elf'    => 'elves',
      'half'   => 'halves',   'calf'   => 'calves',   'wolf'   => 'wolves',   'scarf'  => 'scarves',
      'hoof'   => 'hooves',   'wharf'  => 'wharves',  'menu'   => 'menus',    'guru'   => 'gurus',
      'bureau' => 'bureaus',  'plateau' => 'plateaus', 'chateau' => 'chateaus', 'tableau' => 'tableaus'
    ];

    $regular = [
      'human', 'german', 'roman', 'shaman', 'talisman', 'caiman', 'cayman', 'ottoman',
      'walkman', 'doberman', 'abdomen', 'specimen', 'omen', 'stamen', 'regimen', 'acumen',
      'semen', 'hymen', 'olive'
    ];

    $us = [
      'status', 'bonus', 'campus', 'census', 'chorus', 'circus', 'genius', 'virus', 'bus',
      'minibus', 'omnibus', 'trolleybus', 'apparatus', 'prospectus', 'sinus', 'surplus',
      'walrus', 'octopus', 'platypus', 'lotus', 'abacus', 'consensus', 'nexus', 'plus',
      'minus', 'thesaurus', 'uterus', 'fetus', 'hiatus', 'impetus', 'isthmus', 'onus', 'opus'
    ];

    $tables = [
      'uncountable'        => array_fill_keys ( $uncountable, TRUE ),
      'uncountableEndings' => $uncountableEndings,
      'plural'             => $irregular,
      'singular'           => array_flip ( $irregular ),
      'endings'            => $endings,
      'regular'            => array_fill_keys ( array_merge ( $regular, array_map ( fn ( $one ) => "{$one}s", $regular ) ), TRUE ),
      'us'                 => array_fill_keys ( $us, TRUE )
    ];

    return $tables;

  }

  // A text from what the caller gave: a string as it is, NULL and FALSE '', a number or TRUE
  // what PHP makes of it, an object with __toString its string. Anything else is named with
  // padError - padStrLimit needs a text, not array - and read as ''. With $scrub, bytes
  // that are not valid UTF-8 become '?', so the /u expressions after it never fail; the
  // functions the pipes share pass FALSE and answer what the pipes always answered.

  function padStrText ( $value, $function, $scrub = TRUE ) {

    // NAN is the text NAN, as PHP always wrote it: PHP 8.5 warns when it casts NAN to a
    // string, and the warning ended the request.

    if ( is_string ( $value ) )
      $text = $value;
    elseif ( $value === NULL or $value === FALSE )
      return '';
    elseif ( is_float ( $value ) and is_nan ( $value ) )
      $text = 'NAN';
    elseif ( is_scalar ( $value ) or $value instanceof Stringable )
      $text = (string) $value;
    else {
      padError ( "$function needs a text, not " . padStrShow ( $value ) );
      return '';
    }

    if ( $scrub and ! mb_check_encoding ( $text, 'UTF-8' ) )
      $text = mb_scrub ( $text, 'UTF-8' );

    return $text;

  }

  // A whole number from what the caller gave - an int, a float without a fraction, or a
  // string of digits - for a limit, a length or an index. Anything else, or a negative
  // number where only 0 or more makes sense, is named with padError; the answer is then NULL
  // and the function answers its empty value.

  function padStrWhole ( $value, $function, $what, $negative = FALSE ) {

    $number = NULL;

    if ( is_int ( $value ) )
      $number = max ( $value, -PHP_INT_MAX );
    elseif ( is_float ( $value ) and is_finite ( $value ) and floor ( $value ) == $value and abs ( $value ) < PHP_INT_MAX )
      $number = (int) $value;
    elseif ( is_string ( $value ) and preg_match ( '/^\s*[+-]?\d{1,18}\s*$/', $value ) )
      $number = (int) $value;

    if ( $number !== NULL and ( $negative or $number >= 0 ) )
      return $number;

    padError ( "$function needs a whole number" . ( $negative ? '' : ' of 0 or more' ) . " as its $what, not " . padStrShow ( $value ) );

    return NULL;

  }

  // A value as an error message shows it: a scalar written as PHP would, anything else by
  // its type.

  function padStrShow ( $value ) {

    return is_scalar ( $value ) ? var_export ( $value, TRUE ) : get_debug_type ( $value );

  }

  // The words of a text for the case functions - see the note above padStrCamel. A change of
  // case becomes a space first: a lower case letter or a digit before a capital, and a
  // capital before a capital that starts a word (the L of HTMLParser's Parser stands after
  // HTM-L-P, so HTML and Parser part). An apostrophe inside a word keeps it whole: it's.

  function padStrWordList ( $text ) {

    $text = preg_replace ( '/(?<=[\p{Ll}\p{N}])(?=\p{Lu})|(?<=\p{Lu})(?=\p{Lu}\p{Ll})/u', ' ', $text );

    preg_match_all ( "/[\p{L}\p{N}\p{M}]+(?:['’][\p{L}\p{N}\p{M}]+)*/u", $text, $found );

    return $found [0];

  }

  // The first character in upper case, the rest as it is - mb_ucfirst before PHP 8.4 had it.

  function padStrUpperFirst ( $text ) {

    return mb_strtoupper ( mb_substr ( $text, 0, 1 ) ) . mb_substr ( $text, 1 );

  }

  // The text without whitespace at its start, or at its end - unicode and invisible spaces
  // included (padStrTrims), where PHP's trim knows only ASCII.

  function padStrTrimStart ( $text ) {

    return preg_replace ( '/^[' . padStrTrims . ']+/u', '', $text );

  }

  function padStrTrimEnd ( $text ) {

    return preg_replace ( '/[' . padStrTrims . ']+$/u', '', $text );

  }

?>
