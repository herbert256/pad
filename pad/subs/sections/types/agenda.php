<?php

  // {section:agenda title='Programme', timezone='CET'} {section:item 'Opening', from='09:00',
  // to='09:30', speaker='Ada Lovelace', track='Main hall'}A word of welcome.{/section:item} ...
  // {/section:agenda} - the programme of a conference or a meeting: the sessions by their
  // time, those at the same time side by side, each with its title, its time, its speaker,
  // its track and a line about it; the days under a heading each when it has more than one.
  // The sessions are the rows of data= - their fields title, from, to, day, track, speaker,
  // text, link and pause (a break - coffee, lunch - drawn quieter) - or {section:item} pairs
  // or single tags, the item's first value the title and its content the text. A time is
  // written 09:30, on the clock of 24 hours, or with its day as 2026-11-05T09:30, an offset
  // after it when given; no other way, so every time is read the same. The sessions are put
  // in order by their day, their time, then their track - the tracks in the order tracks=
  // gives them, else as they first come - and as written; a day written 2026-11-05 orders the
  // days by the calendar, any other day stands where it first comes. timezone= says what
  // clock the times are on - CET, UTC+1, Europe/Amsterdam - under the heading; tracks= names
  // every track, and a session in a track it does not name is reported. A session without a
  // time, a time written otherwise, an end before its start, and a session without a day
  // where the others have one are reported too.
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text,
  // timezone, tracks, data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead   = padSectionHead ();
  $padSecZone   = padSectionOption ( 'timezone' );
  $padSecTracks = padTagParm ( 'tracks', '' );
  $padSecTracks = is_array ( $padSecTracks ) ? $padSecTracks : ( is_scalar ( $padSecTracks ) ? explode ( ',', padParmText ( (string) $padSecTracks ) ) : [] );
  $padSecTracks = array_values ( array_unique ( array_filter ( array_map ( fn ( $one ) => is_scalar ( $one ) ? trim ( (string) $one ) : '', $padSecTracks ), 'strlen' ) ) );
  $padSecNamed  = (bool) $padSecTracks;
  $padSecItems  = padSectionItems ( 'title', [ 'title', 'from', 'to', 'day', 'track', 'speaker', 'text', 'link', 'pause' ], 'text', 'sessions' );
  $padSecLevel  = padSectionLevel ();

  // A time: [ minutes from midnight, the day it carries or '', 'HH:MM', the offset or '' ] -
  // or NULL when it is written another way.

  $padSecTime = function ( $text ) {

    $text = trim ( (string) $text );

    if ( preg_match ( '/^([01]?\d|2[0-3]):([0-5]\d)$/D', $text, $m ) )
      return [ (int) $m [1] * 60 + (int) $m [2], '', sprintf ( '%02d:%02d', $m [1], $m [2] ), '' ];

    if ( preg_match ( '/^(\d{4})-(\d{2})-(\d{2})[T ]([01]\d|2[0-3]):([0-5]\d)(?::[0-5]\d)?(Z|[+-](?:[01]\d|2[0-3]):?[0-5]\d)?$/D', $text, $m )
         and checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) )
      return [ (int) $m [4] * 60 + (int) $m [5], "$m[1]-$m[2]-$m[3]", "$m[4]:$m[5]", $m [6] ?? '' ];

    return NULL;

  };

  $padSecRaw = fn ( $item, $field ) => trim ( (string) ( is_scalar ( padSectionRaw ( $item, $field ) ) ? padSectionRaw ( $item, $field ) : '' ) );

  // The sessions as they will be shown: each with its day, its time, its place among the
  // tracks and its place as written, to sort by.

  $padSecSessions = [];
  $padSecDays     = [];
  $padSecDayless  = 0;

  foreach ( $padSecItems as $padSecN => $padSecItem ) {

    $padSecTitle = $padSecRaw ( $padSecItem, 'title' );
    $padSecFrom  = $padSecTime ( $padSecRaw ( $padSecItem, 'from' ) );
    $padSecTo    = $padSecRaw ( $padSecItem, 'to' ) === '' ? NULL : $padSecTime ( $padSecRaw ( $padSecItem, 'to' ) );
    $padSecWhat  = "the session '" . padMakeSafe ( $padSecTitle, 20 ) . "' of the " . padSectionWhat ();

    if ( $padSecFrom === NULL ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( ( $padSecRaw ( $padSecItem, 'from' ) === '' ? "$padSecWhat has no time - from='09:30'"
                                                                : "$padSecWhat has no time '" . padMakeSafe ( $padSecRaw ( $padSecItem, 'from' ), 20 ) . "' - 09:30, or 2026-11-05T09:30 with its day" ) );
      continue;
    }

    if ( $padSecRaw ( $padSecItem, 'to' ) !== '' and $padSecTo === NULL ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "$padSecWhat has no end '" . padMakeSafe ( $padSecRaw ( $padSecItem, 'to' ), 20 ) . "' - 10:15, or 2026-11-05T10:15 with its day" );
    }

    $padSecDay = $padSecRaw ( $padSecItem, 'day' );
    $padSecDay = ( $padSecDay === '' ) ? $padSecFrom [1] : $padSecDay;

    // The end against the start as whole moments: each time on the date it carries, else
    // the start on the session's day when that is a date, else on the end's date, and the
    // end on the start's - so a time with a date and one without, or two dates, are
    // compared as well.

    if ( $padSecTo !== NULL ) {

      $padSecStart = ( $padSecFrom [1] !== '' ) ? $padSecFrom [1] : ( preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', $padSecDay ) ? $padSecDay : '' );
      $padSecEnd   = ( $padSecTo [1] !== '' ) ? $padSecTo [1] : $padSecStart;
      $padSecStart = ( $padSecStart !== '' ) ? $padSecStart : $padSecEnd;

      if ( [ $padSecEnd, $padSecTo [0] ] <= [ $padSecStart, $padSecFrom [0] ] ) {
        $padSecApart = ( $padSecStart !== $padSecEnd );
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "$padSecWhat ends at " . ( $padSecApart ? "$padSecEnd " : '' ) . "{$padSecTo [2]}, not after it starts at "
                     . ( $padSecApart ? "$padSecStart " : '' ) . "{$padSecFrom [2]}" );
        $padSecTo = NULL;
      }

    }

    if ( $padSecDay === '' )
      $padSecDayless++;
    elseif ( ! isset ( $padSecDays [$padSecDay] ) )
      $padSecDays [$padSecDay] = count ( $padSecDays );

    $padSecTrack = $padSecRaw ( $padSecItem, 'track' );

    if ( $padSecTrack !== '' and ! in_array ( $padSecTrack, $padSecTracks, TRUE ) ) {
      if ( $padSecNamed and $GLOBALS ['padCheckSyntax'] )
        padError ( "$padSecWhat is in the track '" . padMakeSafe ( $padSecTrack, 20 ) . "', which tracks= does not name - " . implode ( ', ', array_map ( fn ( $one ) => padMakeSafe ( $one, 20 ), $padSecTracks ) ) );
      $padSecTracks [] = $padSecTrack;
    }

    $padSecSessions [] = [ 'item' => $padSecItem, 'n' => $padSecN, 'day' => $padSecDay, 'from' => $padSecFrom, 'to' => $padSecTo,
                           'track' => $padSecTrack, 'order' => ( $padSecTrack === '' ) ? -1 : array_search ( $padSecTrack, $padSecTracks, TRUE ) ];

  }

  if ( $padSecDayless and $padSecDays and $GLOBALS ['padCheckSyntax'] )
    padError ( "a session of the " . padSectionWhat () . " has no day where the others have one - day= on every session, or on none" );

  // The days by the calendar when every one is a date, else as they first come; a session
  // without a day before them.

  $padSecDated = ( $padSecDays and ! array_filter ( array_keys ( $padSecDays ), fn ( $day ) => ! preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', (string) $day ) ) );

  if ( $padSecDated ) {
    $padSecSorted = array_keys ( $padSecDays );
    sort ( $padSecSorted, SORT_STRING );
    $padSecDays = array_flip ( $padSecSorted );
  }

  usort ( $padSecSessions, fn ( $a, $b ) =>
    [ $a ['day'] === '' ? -1 : $padSecDays [ $a ['day'] ], $a ['from'] [0], $a ['order'], $a ['n'] ]
    <=>
    [ $b ['day'] === '' ? -1 : $padSecDays [ $b ['day'] ], $b ['from'] [0], $b ['order'], $b ['n'] ] );

  // The slots: the sessions of a day that start at the same time, together.

  $padSecSlots = [];

  foreach ( $padSecSessions as $padSecOne )
    $padSecSlots [ $padSecOne ['day'] ] [ $padSecOne ['from'] [2] ] [] = $padSecOne;

  $padSecShowDays = count ( $padSecDays ) > 1;
  $padSecSessLvl  = min ( 6, $padSecLevel + ( $padSecShowDays ? 2 : 1 ) );
  $padSecHtml     = '';

  // A <time> of a session: the day joined to the time when the day is a date, so a machine
  // reads the moment; the clock time alone else.

  $padSecStamp = fn ( $time, $day ) =>
    '<time datetime="' . ( ( $time [1] !== '' or preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', $day ) ) ? ( $time [1] !== '' ? $time [1] : $day ) . "T{$time [2]}" . $time [3] : $time [2] ) . "\">{$time [2]}</time>";

  $padSecRange = fn ( $one ) =>
    $padSecStamp ( $one ['from'], $one ['day'] )
    . ( $one ['to'] ? '<span aria-hidden="true">–</span><span class="ps-sr"> to </span>' . $padSecStamp ( $one ['to'], $one ['day'] ) : '' );

  foreach ( $padSecSlots as $padSecDay => $padSecTimes ) {

    $padSecList = '';

    foreach ( $padSecTimes as $padSecStarts ) {

      // The slot's time is the one its sessions share, with their end when they all end
      // together; a session that ends at another time says so itself.

      $padSecEnds = array_unique ( array_map ( fn ( $one ) => $one ['to'] ? $one ['to'] [2] : '', $padSecStarts ) );
      $padSecSame = count ( $padSecEnds ) == 1;
      $padSecCell = '';

      foreach ( $padSecStarts as $padSecOne ) {

        $padSecItem    = $padSecOne ['item'];
        $padSecPause   = padSectionTrue ( padSectionRaw ( $padSecItem, 'pause', FALSE ) );
        $padSecTitle   = padSectionField ( $padSecItem, 'title' );
        $padSecLink    = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
        $padSecSpeaker = padSectionField ( $padSecItem, 'speaker' );
        $padSecText    = padSectionField ( $padSecItem, 'text' );

        if ( $padSecLink !== '' and $padSecTitle !== '' )
          $padSecTitle = "<a href=\"$padSecLink\">$padSecTitle</a>";

        $padSecMeta = ( ( ! $padSecSame and $padSecOne ['to'] ) ? '<span class="ps-session-time">' . $padSecRange ( $padSecOne ) . '</span>' : '' )
                    . ( $padSecOne ['track'] !== '' ? '<span class="ps-session-track">' . padSectionEscape ( $padSecOne ['track'] ) . '</span>' : '' )
                    . ( $padSecSpeaker !== '' ? "<span class=\"ps-session-speaker\">$padSecSpeaker</span>" : '' );

        $padSecCell .= '<li class="ps-session' . ( $padSecPause ? ' ps-session-pause' : '' ) . '">'
                     . "<h$padSecSessLvl class=\"ps-item-title\">$padSecTitle</h$padSecSessLvl>"
                     . ( $padSecMeta !== '' ? "<p class=\"ps-session-meta\">$padSecMeta</p>" : '' )
                     . ( $padSecText !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                     . '</li>';

      }

      $padSecList .= '<li class="ps-slot"><p class="ps-slot-time">'
                   . ( $padSecSame ? $padSecRange ( $padSecStarts [0] ) : $padSecStamp ( $padSecStarts [0] ['from'], $padSecStarts [0] ['day'] ) )
                   . "</p><ul class=\"ps-sessions\">$padSecCell</ul></li>";

    }

    if ( $padSecShowDays and $padSecDay !== '' ) {
      $padSecDayLvl = min ( 6, $padSecLevel + 1 );
      $padSecLabel  = padSectionEscape ( $padSecDay );
      $padSecHtml  .= "<h$padSecDayLvl class=\"ps-agenda-day\">"
                    . ( preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', $padSecDay ) ? "<time datetime=\"$padSecLabel\">$padSecLabel</time>" : $padSecLabel )
                    . "</h$padSecDayLvl>";
    }

    $padSecHtml .= "<ol class=\"ps-agenda-slots\">$padSecList</ol>";

  }

  $padSecZone = ( $padSecZone !== '' ) ? '<p class="ps-agenda-zone">All times ' . padSectionEscape ( $padSecZone ) . '</p>' : '';

  return padSection ( 'agenda', $padSecHead . "<div class=\"ps-agenda\">$padSecZone$padSecHtml</div>" );

?>
