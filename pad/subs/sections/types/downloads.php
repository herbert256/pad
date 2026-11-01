<?php

  // {section:downloads title='Download PAD'} {section:item 'pad-2.1.0.zip', link='/files/pad-2.1.0.zip',
  // platform='Any system', size=2457600, checksum='sha256:9f86...'}The engine and every
  // application.{/section:item} ... {/section:downloads} - the files to download, a row each:
  // its name, what it is for - the platform, the version, the size, the date - a line about
  // it, its checksum and its button. The files are the rows of data= - their fields name,
  // link, platform, version, size, date, checksum, text, button and featured - or
  // {section:item} pairs or single tags, the item's first value the name and its content the
  // text. A size in bytes - 2457600 - is written as one a person reads, 2.3 MB; another stands
  // as it is. A checksum is the hex digits the publisher gave, the algorithm before them -
  // sha256:9f86... - and shown under a summary of its name to unfold and copy: the section
  // shows what it was given and checks only that the digits are as many as the algorithm makes;
  // it never says a file was verified, which only the one who downloads it can do. One file
  // can be featured - the one most people want, its button in the accent and the others
  // quieter; without one every button is. A file's button is button= ('Download'), named by the
  // file for a screen reader, and shows when it has a link.
  //
  // Parts: eyebrow, title, text, note. Options: title (or the first value), eyebrow, text,
  // note, data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'note' ] );

  include_once PAD . 'subs/icons/_lib/icon.php';

  $padSecHead  = padSectionHead ();
  $padSecNote  = padSectionPart ( 'note' );
  $padSecItems = padSectionItems ( 'name', [ 'name', 'link', 'platform', 'version', 'size', 'date', 'checksum', 'text', 'button', 'featured' ], 'text', 'files' );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecId    = padSectionId ();
  $padSecAny   = FALSE;
  $padSecList  = '';

  // The algorithms a checksum names, with the name a person reads and the hex digits each
  // makes.

  $padSecSums = [ 'md5' => [ 'MD5', 32 ], 'sha1' => [ 'SHA-1', 40 ], 'sha224' => [ 'SHA-224', 56 ], 'sha256' => [ 'SHA-256', 64 ],
                  'sha384' => [ 'SHA-384', 96 ], 'sha512' => [ 'SHA-512', 128 ], 'sha3-256' => [ 'SHA3-256', 64 ],
                  'sha3-512' => [ 'SHA3-512', 128 ], 'blake2b' => [ 'BLAKE2b', 128 ], 'blake3' => [ 'BLAKE3', 64 ] ];

  foreach ( $padSecItems as $padSecItem )
    if ( padSectionTrue ( padSectionRaw ( $padSecItem, 'featured', FALSE ) ) )
      $padSecAny = TRUE;

  foreach ( $padSecItems as $padSecN => $padSecItem ) {

    if ( ! padSectionHas ( $padSecItem, 'name' ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a file of the " . padSectionWhat () . " has no name - the item's first value, or the field name of its row" );
      continue;
    }

    $padSecFeatured = padSectionTrue ( padSectionRaw ( $padSecItem, 'featured', FALSE ) );
    $padSecFileId   = "$padSecId-file-" . ( $padSecN + 1 );
    $padSecName     = padSectionField ( $padSecItem, 'name' );
    $padSecLink     = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecText     = padSectionField ( $padSecItem, 'text' );
    $padSecButton   = padSectionHas ( $padSecItem, 'button' ) ? padSectionField ( $padSecItem, 'button' ) : 'Download';
    $padSecRaw      = fn ( $field ) => trim ( (string) ( is_scalar ( padSectionRaw ( $padSecItem, $field ) ) ? padSectionRaw ( $padSecItem, $field ) : '' ) );

    // What the file is for, a term each - the terms read by a screen reader, the values shown.

    $padSecSize = $padSecRaw ( 'size' );

    if ( preg_match ( '/^\d+$/D', $padSecSize ) )
      $padSecSize = padNumberFileSize ( (int) $padSecSize, ( (int) $padSecSize < 1024 ) ? 0 : 1 );

    $padSecDate = $padSecRaw ( 'date' );
    $padSecMeta = '';

    foreach ( [ 'Platform' => $padSecRaw ( 'platform' ), 'Version' => $padSecRaw ( 'version' ), 'Size' => $padSecSize ] as $padSecTerm => $padSecValue )
      if ( $padSecValue !== '' )
        $padSecMeta .= "<dt class=\"ps-sr\">$padSecTerm</dt><dd>" . padSectionEscape ( $padSecValue ) . '</dd>';

    if ( $padSecDate !== '' )
      $padSecMeta .= '<dt class="ps-sr">Date</dt><dd>'
                   . ( preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', $padSecDate ) ? "<time datetime=\"$padSecDate\">$padSecDate</time>" : padSectionEscape ( $padSecDate ) )
                   . '</dd>';

    // The checksum: algorithm:digits, or the digits alone. Digits that are no hex, or not as
    // many as the algorithm makes, are a checksum copied wrong.

    $padSecSum  = $padSecRaw ( 'checksum' );
    $padSecHash = '';

    if ( $padSecSum !== '' ) {

      [ $padSecAlgo, $padSecDigits ] = str_contains ( $padSecSum, ':' ) ? array_map ( 'trim', explode ( ':', $padSecSum, 2 ) ) : [ '', $padSecSum ];

      $padSecAlgo  = strtolower ( $padSecAlgo );
      $padSecKnown = $padSecSums [ str_replace ( [ '_' ], [ '-' ], $padSecAlgo ) ] ?? $padSecSums [ str_replace ( '-', '', $padSecAlgo ) ] ?? NULL;
      $padSecWrong = '';

      if ( ! preg_match ( '/^[0-9a-fA-F]+$/D', $padSecDigits ) )
        $padSecWrong = 'hex digits, 0 to 9 and a to f';
      elseif ( $padSecAlgo !== '' and $padSecKnown === NULL )
        $padSecWrong = 'an algorithm of ' . implode ( ', ', array_keys ( $padSecSums ) ) . ' before them';
      elseif ( $padSecKnown and strlen ( $padSecDigits ) != $padSecKnown [1] )
        $padSecWrong = $padSecKnown [1] . ' hex digits for ' . $padSecKnown [0] . ', not ' . strlen ( $padSecDigits );

      if ( $padSecWrong !== '' ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "the file '" . padMakeSafe ( $padSecRaw ( 'name' ), 20 ) . "' of the " . padSectionWhat () . " has no checksum '" . padMakeSafe ( $padSecSum, 20 ) . "' - $padSecWrong" );
      } else
        $padSecHash = '<details class="ps-download-sum"><summary>' . ( $padSecKnown ? $padSecKnown [0] : 'Checksum' ) . '</summary>'
                    . '<code>' . strtolower ( $padSecDigits ) . '</code></details>';

    }

    $padSecClass = ( $padSecFeatured or ! $padSecAny ) ? 'ps-button' : 'ps-button ps-button-second';

    $padSecList .= '<li class="ps-card ps-download' . ( $padSecFeatured ? ' ps-download-featured' : '' ) . '">'
                 . '<span class="ps-download-icon">' . padIcon ( 'download', 22 ) . '</span>'
                 . '<div class="ps-download-about">'
                 . "<h$padSecLevel class=\"ps-item-title\" id=\"$padSecFileId\">$padSecName</h$padSecLevel>"
                 . ( $padSecMeta !== '' ? "<dl class=\"ps-download-meta\">$padSecMeta</dl>" : '' )
                 . ( $padSecText !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                 . $padSecHash
                 . '</div>'
                 . ( $padSecLink !== '' ? "<a class=\"$padSecClass\" href=\"$padSecLink\" aria-describedby=\"$padSecFileId\">$padSecButton</a>" : '' )
                 . '</li>';

  }

  return padSection ( 'downloads', $padSecHead . "<ul class=\"ps-downloads\">$padSecList</ul>"
                                 . ( $padSecNote !== '' ? "<p class=\"ps-note ps-downloads-note\">$padSecNote</p>" : '' ) );

?>
