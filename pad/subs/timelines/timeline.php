<?php

  // Shared event parsing for the timeline views. Existing horizontal/vertical SVG stays
  // unchanged; roadmap and swimlane use the same dates, spans, labels and colour groups.
  include_once PAD . 'subs/timelines/_lib/timeline.php';
  include_once PAD . 'subs/timelines/_lib/views.php';

  if ( ! padKind ( 'timelines', $padTimelineKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no timeline type named '" . padMakeSafe ( $padTimelineKind, 20 ) . "'" );
    return FALSE;
  }

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'timeline', $padTimelineKind );

  $padTimelineSource = $padContent;
  $padContent        = '';
  $padTimelineRows   = padChartRows ( $padTimelineSource, 'timeline' );
  $padData [$pad]    = padDefaultData ();

  // Explicit record views validate their own fixed field contracts.
  if ( in_array ( $padTimelineKind, [ 'audit', 'rangecompare' ], TRUE ) )
    return include PAD . "subs/timelines/types/$padTimelineKind.php";

  // The fields: a date= field else the first that reads as a date, then the texts. A bare
  // number of up to four digits is a year only when no field reads as a date: rows as a
  // database gives them, an id first, were drawn in the years 0001, 0002 ...

  $padTimelineDates = $padTimelineYears = $padTimelineTexts = [];

  // A date object of the page's PHP is a date field too: read only when date= named it, a
  // timeline of such rows without date= found no date and failed.

  foreach ( padChartRow ( reset ( $padTimelineRows ) ?: [] ) as $padTimelineKey => $padTimelineField )
    if ( $padTimelineField instanceof DateTimeInterface )
      $padTimelineDates [] = (string) $padTimelineKey;
    elseif ( is_scalar ( $padTimelineField ) and ! is_numeric ( $padTimelineField ) and padTimelineDate ( $padTimelineField ) !== FALSE )
      $padTimelineDates [] = (string) $padTimelineKey;
    elseif ( is_scalar ( $padTimelineField ) and preg_match ( '/^\d{1,4}$/', trim ( (string) $padTimelineField ) ) and padTimelineDate ( $padTimelineField ) !== FALSE )
      $padTimelineYears [] = (string) $padTimelineKey;
    elseif ( is_scalar ( $padTimelineField ) and ! is_numeric ( $padTimelineField ) )
      $padTimelineTexts [] = (string) $padTimelineKey;

  if ( ! $padTimelineDates )
    $padTimelineDates = $padTimelineYears;

  $padTimelineFrom    = (string) padTagParm ( 'from' );
  $padTimelineTo      = (string) padTagParm ( 'to' );
  $padTimelineColor   = (string) padTagParm ( 'color' );
  $padTimelineLane = in_array ( $padTimelineKind, [ 'roadmap', 'swimlane' ], TRUE ) ? (string) padTagParm ( 'lane', '' ) : '';
  $padTimelineMilestone = in_array ( $padTimelineKind, [ 'roadmap', 'swimlane' ], TRUE ) ? (string) padTagParm ( 'milestone', '' ) : '';
  $padTimelineUsed    = [ $padTimelineFrom, $padTimelineTo, $padTimelineColor, $padTimelineLane, $padTimelineMilestone ];
  $padTimelineAt      = ( $padTimelineFrom !== '' and $padTimelineTo !== '' ) ? (string) padTagParm ( 'date' ) : padChartPick ( 'date', $padTimelineDates, $padTimelineUsed );
  $padTimelineUsed [] = $padTimelineAt;
  $padTimelineLabel   = padChartPick ( 'label', $padTimelineTexts, $padTimelineUsed );
  $padTimelineText    = padChartPick ( 'text',  $padTimelineTexts, $padTimelineUsed );

  $padTimelineEvents = padTimelineEvents ( $padTimelineRows, $padTimelineAt, $padTimelineFrom, $padTimelineTo,
                                           $padTimelineLabel, $padTimelineText, $padTimelineColor, (string) padTagParm ( 'format' ) );

  if ( ! $padTimelineEvents ) {
    if ( $padCheckSyntax and $padTimelineRows )
      padError ( "the timeline has no events with a date - date='field', or from='field' and to='field' for spans" );
    return FALSE;
  }

  // A row without a value in the color= field is drawn in the colour of the rest and has no
  // legend entry, where it was a group of its own - a swatch with no name. Without any
  // group, every row has the first colour.

  $padTimelineGroups = [];

  foreach ( $padTimelineEvents as $padTimelineEvent )
    if ( $padTimelineEvent [5] !== '' and ! isset ( $padTimelineGroups [ $padTimelineEvent [5] ] ) )
      $padTimelineGroups [ $padTimelineEvent [5] ] = count ( $padTimelineGroups );

  if ( in_array ( '', array_column ( $padTimelineEvents, 5 ), TRUE ) )
    $padTimelineGroups [''] = $padTimelineGroups ? 8 : 0;

  $padTimelineTitle = (string) padTagParm ( 'title', 'Timeline' );

  return include PAD . "subs/timelines/types/$padTimelineKind.php";

?>
