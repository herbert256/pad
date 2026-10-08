<?php

  // A share is the same answer every time for one id and about that share of a thousand
  // ids; two flags pick their shares apart, and raising one keeps those who had it. An override fixes a flag until it is freed.
  // A function flag is given the user, NULL for a guest.

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  function featureCaseGuest ( $user ) { return $user === NULL; }

  $padFeatures = [ 'quarter' => 0.25, 'half' => 0.5, 'on' => TRUE, 'guests' => 'featureCaseGuest' ];

  $quarter = $half = $lost = $both = 0;
  $stable  = TRUE;
  $had     = [];

  for ( $i = 0; $i < 1000; $i++ ) {
    padFeatureId ( "id-$i" );
    $had [$i] = padFeature ( 'quarter' );
    $quarter += $had [$i] ? 1 : 0;
    $half    += padFeature ( 'half' ) ? 1 : 0;
    $both    += ( $had [$i] && padFeature ( 'half' ) ) ? 1 : 0;
    $stable   = $stable && ( $had [$i] === padFeature ( 'quarter' ) );
  }

  // The quarter raised to a half: everyone who had it keeps it.

  $padFeatures ['quarter'] = 0.5;

  for ( $i = 0; $i < 1000; $i++ ) {
    padFeatureId ( "id-$i" );
    $lost += ( $had [$i] && ! padFeature ( 'quarter' ) ) ? 1 : 0;
  }

  $shares = ( ( $quarter > 200 && $quarter < 300 ) ? 'about a quarter' : $quarter ) . ', '
          . ( ( $half > 450 && $half < 550 ) ? 'about half' : $half ) . ', '
          . ( ( $both > 80 && $both < 170 ) ? 'two flags pick apart' : "$both in both" ) . ', '
          . ( $lost == 0 ? 'raised keeps them all' : "$lost lost" ) . ', ' . ( $stable ? 'stable' : 'unstable' );

  padFeatureOverride ( 'on', FALSE );
  $over = $yes ( padFeature ( 'on' ) );
  padFeatureOverride ( 'on', NULL );
  $over .= ' ' . $yes ( padFeature ( 'on' ) );

  $fn = $yes ( padFeature ( 'guests' ) );
  padLogin ( [ 'id' => 2 ] );
  $fn .= ' ' . $yes ( padFeature ( 'guests' ) );

?>
