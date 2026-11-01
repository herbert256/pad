<?php

  // vCard (RFC 6350, version 4.0 - 3.0 is read as well), the reader and the writer of
  // types/vcf.php.
  //
  // The reader takes every card of the text, each a row with the same fields, empty where
  // the card has none:
  //
  //   name, family, given, additional, prefix, suffix, nickname, org, title, email, emails,
  //   phone, phones, url, street, city, region, postcode, country, birthday, note, uid
  //
  // name is FN, the five after it the parts of N; org its units joined by ', '. email and
  // phone are the preferred address and number - the lowest PREF, a TYPE=pref of version 3,
  // else the first - and emails and phones all of them, joined by ', ', the preferred first.
  // The address is the preferred ADR's parts; birthday is BDAY, 19960415 written 1996-04-15.
  // A value is unescaped - \n a newline, \, a comma; a TEL's tel: is left off.
  //
  // The writer writes a version 4.0 card per row with a name - FN is the one field a card
  // must have: name, else given and family, else org, else email - every text escaped, every
  // line folded at 75 octets and ended with CRLF. The UID is the row's uid, or a urn:uuid made
  // of the name, the e-mail and the phone: the same rows, the same bytes.

  include_once PAD . 'subs/formats/_lib/lines.php';

  function padVcfRead ( $text ) {

    $lines = padLinesRead ( $text );

    if ( ! $lines or $lines [0] [1] != 'BEGIN' or strtoupper ( trim ( $lines [0] [3] ) ) != 'VCARD' )
      padFormatFail ( $lines [0] [0] ?? 1, 'a vCard text opens with BEGIN:VCARD' );

    $rows  = [];
    $card  = NULL;
    $depth = 0;

    foreach ( $lines as [ $at, $name, $params, $value ] ) {

      $what = strtoupper ( trim ( $value ) );

      if ( $name == 'BEGIN' and $what == 'VCARD' ) {
        if ( $depth++ == 0 )
          $card = [];
      } elseif ( $name == 'END' and $what == 'VCARD' ) {
        if ( $depth == 0 )
          padFormatFail ( $at, 'END:VCARD closes nothing' );
        if ( --$depth == 0 )
          $rows [] = padVcfCard ( $card );
      } elseif ( $depth == 0 )
        padFormatFail ( $at, "$name stands outside a card - between BEGIN:VCARD and END:VCARD" );
      elseif ( $depth == 1 )
        $card [$name] [] = [ $params, $value ];

    }

    if ( $depth )
      padFormatFail ( end ( $lines ) [0], 'BEGIN:VCARD is not closed by END:VCARD' );

    return $rows;

  }

  function padVcfCard ( $card ) {

    $text  = fn ( $name ) => isset ( $card [$name] ) ? padLinesText ( $card [$name] [0] [1] ) : '';
    $parts = fn ( $name, $count ) => array_pad ( array_map ( fn ( $one ) => padLinesText ( $one ),
                                                 isset ( $card [$name] ) ? padLinesSplit ( padVcfPreferred ( $card [$name] ) [1], ';' ) : [] ),
                                                 $count, '' );

    $n   = $parts ( 'N', 5 );
    $adr = $parts ( 'ADR', 7 );

    $emails = padVcfAll ( $card ['EMAIL'] ?? [], FALSE );
    $phones = padVcfAll ( $card ['TEL']   ?? [], TRUE );

    $birthday = $text ( 'BDAY' );

    if ( preg_match ( '/^(\d{4})(\d\d)(\d\d)$/D', $birthday, $m ) )
      $birthday = "{$m[1]}-{$m[2]}-{$m[3]}";
    elseif ( preg_match ( '/^--(\d\d)(\d\d)$/D', $birthday, $m ) )
      $birthday = "--{$m[1]}-{$m[2]}";

    return [
      'name'       => $text ( 'FN' ),
      'family'     => $n [0],
      'given'      => $n [1],
      'additional' => $n [2],
      'prefix'     => $n [3],
      'suffix'     => $n [4],
      'nickname'   => $text ( 'NICKNAME' ),
      'org'        => implode ( ', ', array_filter ( array_map ( 'padLinesText', isset ( $card ['ORG'] ) ? padLinesSplit ( $card ['ORG'] [0] [1], ';' ) : [] ), 'strlen' ) ),
      'title'      => $text ( 'TITLE' ),
      'email'      => $emails [0] ?? '',
      'emails'     => implode ( ', ', $emails ),
      'phone'      => $phones [0] ?? '',
      'phones'     => implode ( ', ', $phones ),
      'url'        => trim ( $card ['URL'] [0] [1] ?? '' ),
      'street'     => trim ( implode ( ', ', array_filter ( [ $adr [2], $adr [1] ], 'strlen' ) ) ),
      'city'       => $adr [3],
      'region'     => $adr [4],
      'postcode'   => $adr [5],
      'country'    => $adr [6],
      'birthday'   => $birthday,
      'note'       => $text ( 'NOTE' ),
      'uid'        => trim ( $card ['UID'] [0] [1] ?? '' )
    ];

  }

  // The values of a property, the preferred first: the lowest PREF (4.0), a TYPE=pref (3.0),
  // else in the order written.

  function padVcfAll ( $list, $tel ) {

    usort ( $list, fn ( $a, $b ) => padVcfRank ( $a ) <=> padVcfRank ( $b ) );

    $values = [];

    foreach ( $list as [ , $value ] ) {
      $value = padLinesText ( trim ( $value ) );
      if ( $tel and stripos ( $value, 'tel:' ) === 0 )
        $value = substr ( $value, 4 );
      if ( $value !== '' )
        $values [] = $value;
    }

    return $values;

  }

  function padVcfRank ( $one ) {

    $params = $one [0];

    if ( isset ( $params ['PREF'] ) )
      return (int) $params ['PREF'] [0];

    foreach ( $params ['TYPE'] ?? [] as $type )
      if ( strtolower ( $type ) == 'pref' )
        return 1;

    return 101;

  }

  function padVcfPreferred ( $list ) {

    usort ( $list, fn ( $a, $b ) => padVcfRank ( $a ) <=> padVcfRank ( $b ) );

    return $list [0];

  }

  function padVcfWrite ( $rows ) {

    $out = '';

    foreach ( $rows as $index => $row ) {

      $pick   = fn ( ...$names ) => padFormatPick ( $row, $names );
      $given  = $pick ( 'given', 'first_name', 'firstname' );
      $family = $pick ( 'family', 'last_name', 'lastname', 'surname' );
      $name   = $pick ( 'name', 'fn', 'full_name' ) ?: trim ( "$given $family" ) ?: $pick ( 'org', 'company' ) ?: $pick ( 'email' );

      if ( $name === '' ) {
        padFormatSkip ( 'row ' . ( $index + 1 ) . ' has no name a card can have - name, given and family, org or email' );
        continue;
      }

      $emails = padVcfList ( $pick ( 'email' ), $pick ( 'emails' ) );
      $phones = padVcfList ( $pick ( 'phone', 'tel' ), $pick ( 'phones' ) );

      $out .= padLinesFold ( 'BEGIN:VCARD' )
            . padLinesFold ( 'VERSION:4.0' )
            . padLinesFold ( 'FN:' . padLinesEscape ( $name ) );

      $n = [ $family, $given, $pick ( 'additional' ), $pick ( 'prefix' ), $pick ( 'suffix' ) ];

      if ( implode ( '', $n ) !== '' )
        $out .= padLinesFold ( 'N:' . implode ( ';', array_map ( 'padLinesEscape', $n ) ) );

      foreach ( [ 'NICKNAME' => $pick ( 'nickname' ), 'ORG' => $pick ( 'org', 'company' ), 'TITLE' => $pick ( 'title', 'job' ) ] as $prop => $value )
        if ( $value !== '' )
          $out .= padLinesFold ( "$prop:" . padLinesEscape ( $value ) );

      foreach ( $emails as $at => $email )
        $out .= padLinesFold ( 'EMAIL' . ( $at == 0 && count ( $emails ) > 1 ? ';PREF=1' : '' ) . ':' . padLinesEscape ( $email ) );

      foreach ( $phones as $at => $phone )
        $out .= padLinesFold ( 'TEL' . ( $at == 0 && count ( $phones ) > 1 ? ';PREF=1' : '' ) . ':' . padLinesEscape ( $phone ) );

      if ( ( $url = $pick ( 'url', 'website' ) ) !== '' )
        $out .= padLinesFold ( 'URL:' . preg_replace ( '/[\r\n]/', '', $url ) );

      $adr = [ '', '', $pick ( 'street', 'address' ), $pick ( 'city', 'locality' ), $pick ( 'region', 'state' ), $pick ( 'postcode', 'zip' ), $pick ( 'country' ) ];

      if ( implode ( '', $adr ) !== '' )
        $out .= padLinesFold ( 'ADR:' . implode ( ';', array_map ( 'padLinesEscape', $adr ) ) );

      if ( ( $birthday = $pick ( 'birthday', 'bday' ) ) !== '' )
        $out .= padLinesFold ( 'BDAY:' . ( preg_match ( '/^(\d{4})-(\d\d)-(\d\d)$/D', $birthday, $m ) ? "{$m[1]}{$m[2]}{$m[3]}"
                                        : ( preg_match ( '/^--(\d\d)-(\d\d)$/D', $birthday, $m ) ? "--{$m[1]}{$m[2]}" : padLinesEscape ( $birthday ) ) ) );

      if ( ( $note = $pick ( 'note', 'notes' ) ) !== '' )
        $out .= padLinesFold ( 'NOTE:' . padLinesEscape ( $note ) );

      // A UID of 4.0 is a URI, no TEXT to escape: its line breaks are taken out, as a URL's,
      // so a uid of CRLF and a property behind it is one line, never another property.

      $uid = preg_replace ( '/[\r\n]/', '', $pick ( 'uid' ) );

      $out .= padLinesFold ( 'UID:' . ( $uid !== '' ? $uid : 'urn:uuid:' . padFormatUuid ( $name . '|' . ( $emails [0] ?? '' ) . '|' . ( $phones [0] ?? '' ) ) ) )
            . padLinesFold ( 'END:VCARD' );

    }

    return $out;

  }

  // The addresses of a row: the preferred one, then the rest of the list - as the reader
  // writes them, joined by ', ' - each once.

  function padVcfList ( $one, $all ) {

    $list = ( $one !== '' ) ? [ $one ] : [];

    foreach ( explode ( ',', $all ) as $more )
      if ( ( $more = trim ( $more ) ) !== '' and ! in_array ( $more, $list, TRUE ) )
        $list [] = $more;

    return $list;

  }

?>
