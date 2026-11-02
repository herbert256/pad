<?php

  // {widget:ratingdistribution data='reviews', score='stars', count='votes'/} - a score histogram with vote counts, percentages and a weighted average. Without count= every row is one vote; max= is 5 by default, at most 20.
  include_once PAD . 'subs/charts/_lib/chart.php';
  $padRatingRows = padChartRows ( $padContent, 'rating distribution' );
  $padContent = '';
  $padData [$pad] = padDefaultData ();
  $padRatingMax = padTagParm ( 'max', 5 );
  if ( ! is_numeric ( $padRatingMax ) or $padRatingMax != (int) $padRatingMax or $padRatingMax < 1 or $padRatingMax > 20 ) {
    if ( $padCheckSyntax ) padError ( 'rating distribution max must be an integer from 1 to 20' );
    return '';
  }
  $padRatingBuckets = array_fill ( 1, (int) $padRatingMax, 0 );
  $padRatingScoreField = padWidgetText ( 'score', 'score' );
  $padRatingCountField = padWidgetText ( 'count' );
  foreach ( $padRatingRows as $padRatingRow ) {
    $padRatingRow = padChartRow ( $padRatingRow );
    $padRatingScore = $padRatingRow [$padRatingScoreField] ?? NULL;
    $padRatingCount = $padRatingCountField === '' ? 1 : ( $padRatingRow [$padRatingCountField] ?? NULL );
    if ( ! is_numeric ( $padRatingScore ) or $padRatingScore != (int) $padRatingScore or $padRatingScore < 1 or $padRatingScore > $padRatingMax
         or ! is_numeric ( $padRatingCount ) or ! is_finite ( (float) $padRatingCount ) or $padRatingCount < 0 or $padRatingCount != (int) $padRatingCount ) {
      if ( $padCheckSyntax ) padError ( 'rating distribution needs whole scores within max and nonnegative whole vote counts' );
      return '';
    }
    $padRatingBuckets [(int) $padRatingScore] += (int) $padRatingCount;
  }
  $padRatingTotal = array_sum ( $padRatingBuckets );
  $padRatingSum = 0;
  foreach ( $padRatingBuckets as $padRatingScore => $padRatingCount ) $padRatingSum += $padRatingScore * $padRatingCount;
  $padRatingAverage = $padRatingTotal ? round ( $padRatingSum / $padRatingTotal, 2 ) : 0;
  $padRatingHtml = '<table class="pad-rating-distribution"><caption>' . padChartAttr ( padWidgetText ( 'label', 'Ratings' ) )
    . ' — ' . $padRatingTotal . ' votes, average ' . $padRatingAverage . ' / ' . $padRatingMax . '</caption><thead><tr><th scope="col">Score</th><th scope="col">Distribution</th><th scope="col">Votes</th></tr></thead><tbody>';
  foreach ( array_reverse ( $padRatingBuckets, TRUE ) as $padRatingScore => $padRatingCount ) {
    $padRatingPercent = $padRatingTotal ? round ( $padRatingCount / $padRatingTotal * 100, 1 ) : 0;
    $padRatingHtml .= '<tr><th scope="row">' . $padRatingScore . '</th><td><meter min="0" max="' . max ( 1, $padRatingTotal ) . '" value="' . $padRatingCount
      . '" aria-label="' . $padRatingScore . ' stars: ' . $padRatingPercent . '%">' . $padRatingPercent . '%</meter></td><td>' . $padRatingCount . '</td></tr>';
  }
  return $padRatingHtml . '</tbody></table>';

?>
