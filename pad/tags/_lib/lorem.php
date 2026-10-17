<?php

  // Placeholder text - the {lorem} tag. Lorem ipsum as words, sentences or paragraphs, the
  // classic "Lorem ipsum dolor sit amet" first, the rest picked from a Latin word list by
  // the sequence of lib/fake.php - the same text for the same seed, on every machine.
  //
  //   {lorem}                         50 words
  //   {lorem words=12}  {lorem sentences=3}  {lorem paragraphs=4, seed=7}
  //
  // padLorem            the text: words and sentences plain, paragraphs as <p> elements
  // padLoremSentence    one sentence of so many words, a comma now and then, a full stop
  //
  // The seed starts the fake sequence for this text alone: the state it was in is put back
  // afterwards, so a page that seeds padFake* for its own data gets the same values with or
  // without a {lorem} in between.

  const PAD_LOREM_START = [ 'lorem', 'ipsum', 'dolor', 'sit', 'amet,', 'consectetur', 'adipiscing', 'elit' ];

  const PAD_LOREM_WORDS = [
    'a', 'ac', 'accumsan', 'ad', 'aenean', 'aliquam', 'aliquet', 'ante', 'aptent', 'arcu', 'at', 'auctor', 'augue',
    'bibendum', 'blandit', 'class', 'commodo', 'condimentum', 'congue', 'consequat', 'conubia', 'convallis', 'cras',
    'cubilia', 'curabitur', 'curae', 'cursus', 'dapibus', 'diam', 'dictum', 'dictumst', 'dignissim', 'dis', 'donec',
    'dui', 'duis', 'efficitur', 'egestas', 'eget', 'eleifend', 'elementum', 'enim', 'erat', 'eros', 'est', 'et', 'etiam',
    'eu', 'euismod', 'ex', 'facilisi', 'facilisis', 'fames', 'faucibus', 'felis', 'fermentum', 'feugiat', 'finibus',
    'fringilla', 'fusce', 'gravida', 'habitant', 'habitasse', 'hac', 'hendrerit', 'himenaeos', 'iaculis', 'id',
    'imperdiet', 'in', 'inceptos', 'integer', 'interdum', 'justo', 'lacinia', 'lacus', 'laoreet', 'lectus', 'leo',
    'libero', 'ligula', 'litora', 'lobortis', 'luctus', 'maecenas', 'magna', 'magnis', 'malesuada', 'massa', 'mattis',
    'mauris', 'maximus', 'metus', 'mi', 'molestie', 'mollis', 'montes', 'morbi', 'mus', 'nam', 'nascetur', 'natoque',
    'nec', 'neque', 'netus', 'nibh', 'nisi', 'nisl', 'non', 'nostra', 'nulla', 'nullam', 'nunc', 'odio', 'orci',
    'ornare', 'parturient', 'pellentesque', 'penatibus', 'per', 'pharetra', 'phasellus', 'placerat', 'platea',
    'porta', 'porttitor', 'posuere', 'potenti', 'praesent', 'pretium', 'primis', 'proin', 'pulvinar', 'purus', 'quam',
    'quis', 'quisque', 'rhoncus', 'ridiculus', 'risus', 'rutrum', 'sagittis', 'sapien', 'scelerisque', 'sed', 'sem',
    'semper', 'senectus', 'sociosqu', 'sodales', 'sollicitudin', 'suscipit', 'suspendisse', 'taciti', 'tellus',
    'tempor', 'tempus', 'tincidunt', 'torquent', 'tortor', 'tristique', 'turpis', 'ullamcorper', 'ultrices',
    'ultricies', 'urna', 'ut', 'varius', 'vehicula', 'vel', 'velit', 'venenatis', 'vestibulum', 'vitae', 'vivamus',
    'viverra', 'volutpat', 'vulputate' ];

  // $kind is words, sentences or paragraphs, $count how many; $per the sentences of a
  // paragraph, 0 for four to seven of them. Fewer than five words are a title: no full
  // stop. $varied leaves the classic opening out.

  function padLorem ( $kind, $count, $per, $seed, $varied = FALSE ) {

    $state = padFakeState ();

    padFakeSeed ( "lorem:$seed" );

    $first = ! $varied;

    if ( $kind == 'words' and $count < 5 )
      $text = rtrim ( padLoremSentence ( $count, $first ), '.' );
    elseif ( $kind == 'words' )
      $text = padLoremWords ( $count, $first );
    elseif ( $kind == 'sentences' )
      $text = padLoremSentences ( $count, $first );
    else {
      $list = [];
      for ( $i = 0; $i < $count; $i++ )
        $list [] = '<p>' . padLoremSentences ( $per ?: padFakeNumber ( 4, 7 ), $first ) . '</p>';
      $text = implode ( "\n", $list );
    }

    padFakeState ( $state );

    return $text;

  }

  // So many words exactly, as sentences of six to fourteen words - the last one what is left.

  function padLoremWords ( $count, &$first ) {

    $list = [];

    while ( $count > 0 ) {
      $size   = min ( $count, padFakeNumber ( 6, 14 ) );
      $list[] = padLoremSentence ( $size, $first );
      $count -= $size;
    }

    return implode ( ' ', $list );

  }

  function padLoremSentences ( $count, &$first ) {

    $list = [];

    for ( $i = 0; $i < $count; $i++ )
      $list [] = padLoremSentence ( padFakeNumber ( 6, 14 ), $first );

    return implode ( ' ', $list );

  }

  // The first sentence of a text starts with the classic words, as many as it has room for.

  function padLoremSentence ( $size, &$first ) {

    $words = [];

    if ( $first ) {
      $words = array_slice ( PAD_LOREM_START, 0, $size );
      $first = FALSE;
    }

    $from = max ( 2, count ( $words ) );

    while ( count ( $words ) < $size )
      $words [] = padFakePick ( PAD_LOREM_WORDS );

    // A comma after a word now and then, never in the classic opening, after the first two
    // or the last two words.

    for ( $i = $from; $i < $size - 2; $i++ )
      if ( padFakeNumber ( 1, 100 ) <= 12 and ! str_ends_with ( $words [$i - 1], ',' ) and ! str_ends_with ( $words [$i], ',' ) )
        $words [$i] .= ',';

    $words [0] = ucfirst ( $words [0] );

    return rtrim ( implode ( ' ', $words ), ',' ) . '.';

  }

?>
