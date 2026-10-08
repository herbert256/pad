<?php

  // {rating 4.5} - a score as stars, inline SVG by lib/rating.php: the whole ones filled, a
  // half or any other part of one clipped to the score, and the score in words for a screen
  // reader - "4.5 out of 5", or label= instead. max= is the number of stars (default 5, at
  // most 20), size= their height in pixels (default 20) and icon= star (default) or heart.
  //
  //   {rating 4.5, max=5}
  //   {reviews}{rating $stars, size=16} {$text}{/reviews}

  $padRatingValue = trim ( (string) $padParm );
  $padRatingMax   = padTagParm ( 'max', 5 );
  $padRatingIcon  = strtolower ( trim ( (string) padTagParm ( 'icon', 'star' ) ) );

  if ( ! is_numeric ( $padRatingValue ) ) {
    if ( $padCheckSyntax )
      padError ( "the rating has no number for its score: '" . padMakeSafe ( $padRatingValue, 20 ) . "' - {rating 4.5}" );
    $padRatingValue = 0;
  }

  if ( ! is_numeric ( $padRatingMax ) or $padRatingMax != (int) $padRatingMax or $padRatingMax < 1 or $padRatingMax > 20 ) {
    if ( $padCheckSyntax )
      padError ( "the rating has no usable max '" . padMakeSafe ( $padRatingMax, 20 ) . "' - a whole number from 1 to 20" );
    $padRatingMax = 5;
  }

  if ( ! in_array ( $padRatingIcon, PAD_RATING_ICONS ) ) {
    if ( $padCheckSyntax )
      padError ( "the rating has no icon '" . padMakeSafe ( $padRatingIcon, 20 ) . "' - " . implode ( ' or ', PAD_RATING_ICONS ) );
    $padRatingIcon = 'star';
  }

  return padRating ( $padRatingValue, $padRatingMax, $padRatingIcon, (int) padTagParm ( 'size', 20 ), (string) padTagParm ( 'label' ) );

?>
