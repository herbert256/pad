<?php

  // {timeline data='releases', date='when', label='what', text='detail'} - events on a time
  // axis as inline SVG, drawn by lib/timeline.php. data= names the rows as for {chart} - a
  // {data} store, a page array or a _data file - and as a pair the content is the rows,
  // JSON, YAML, XML or CSV:
  //
  //   {timeline date='year', label='what'}
  //     year,what
  //     1969,Moon landing
  //   {/timeline}
  //
  // date= names the field of a moment - a year (1969), a month (2026-10) or anything
  // strtotime reads - else the first field that reads as one; from= and to= name the
  // fields of a span, drawn as a bar. label= names the title of an event and text= the
  // line under it, else the first other text fields. color= groups the events by a field,
  // a colour each and a legend; format= writes the dates with a PHP date format.
  //
  // Across the page by default: a time axis with round ticks - years, months or days by
  // the span - the cards above and below it, out of each other's way. vertical lays the
  // events one under the other instead, dates left and cards right, for a long list.
  // width= is the width (720, vertical 600), height= follows from the cards unless given;
  // title= names the timeline for a screen reader. A timeline without events answers
  // nothing, and its @else@ shows.

  $padTimelineSource = $padContent;
  $padContent        = '';
  $padTimelineRows   = padChartRows ( $padTimelineSource, 'timeline' );
  $padData [$pad]    = padDefaultData ();

  // The fields: a date= field else the first that reads as a date, then the texts.

  $padTimelineDates = $padTimelineTexts = [];

  foreach ( padChartRow ( reset ( $padTimelineRows ) ?: [] ) as $padTimelineKey => $padTimelineField )
    if ( is_scalar ( $padTimelineField ) and padTimelineDate ( $padTimelineField ) !== FALSE and ( ! is_numeric ( $padTimelineField ) or preg_match ( '/^\d{1,4}$/', trim ( $padTimelineField ) ) ) )
      $padTimelineDates [] = (string) $padTimelineKey;
    elseif ( is_scalar ( $padTimelineField ) and ! is_numeric ( $padTimelineField ) )
      $padTimelineTexts [] = (string) $padTimelineKey;

  $padTimelineFrom    = (string) padTagParm ( 'from' );
  $padTimelineTo      = (string) padTagParm ( 'to' );
  $padTimelineColor   = (string) padTagParm ( 'color' );
  $padTimelineUsed    = [ $padTimelineFrom, $padTimelineTo, $padTimelineColor ];
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

  $padTimelineGroups = [];

  foreach ( $padTimelineEvents as $padTimelineEvent )
    if ( ! isset ( $padTimelineGroups [ $padTimelineEvent [5] ] ) )
      $padTimelineGroups [ $padTimelineEvent [5] ] = count ( $padTimelineGroups );

  $padTimelineTitle = (string) padTagParm ( 'title', 'Timeline' );

  if ( padTagParm ( 'vertical', FALSE ) )
    return padTimelineColumn ( $padTimelineEvents, $padTimelineGroups, $padTimelineTitle, max ( 200, (int) padTagParm ( 'width', 600 ) ) );

  return padTimeline ( $padTimelineEvents, $padTimelineGroups, $padTimelineTitle,
                       max ( 200, (int) padTagParm ( 'width', 720 ) ), (int) padTagParm ( 'height', 0 ) );

?>
