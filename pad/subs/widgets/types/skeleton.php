<?php

  // {widget:skeleton shape='card', lines=3, label='Loading', animate/} - a loading placeholder shaped as text, card, avatar or table. lines= is 1 to 20; columns= for a table is 1 to 12. Animation respects reduced motion.
  $padSkeletonShape = padWidgetText ( 'shape', 'text' );
  $padSkeletonLines = padTagParm ( 'lines', 3 );
  $padSkeletonColumns = padTagParm ( 'columns', 3 );
  if ( ! in_array ( $padSkeletonShape, [ 'text', 'card', 'avatar', 'table' ], TRUE )
       or ! is_numeric ( $padSkeletonLines ) or $padSkeletonLines != (int) $padSkeletonLines or $padSkeletonLines < 1 or $padSkeletonLines > 20
       or ! is_numeric ( $padSkeletonColumns ) or $padSkeletonColumns != (int) $padSkeletonColumns or $padSkeletonColumns < 1 or $padSkeletonColumns > 12 ) {
    if ( $padCheckSyntax ) padError ( 'skeleton needs shape text, card, avatar or table; lines 1..20 and columns 1..12' );
    return '';
  }
  $padSkeletonHtml = '<div class="pad-skeleton sk-' . $padSkeletonShape . ( padTagParm ( 'animate', FALSE ) ? ' sk-animate' : '' ) . '" role="status" aria-label="'
    . htmlspecialchars ( padWidgetText ( 'label', 'Loading' ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '" aria-busy="true"><div aria-hidden="true">';
  if ( $padSkeletonShape === 'avatar' ) $padSkeletonHtml .= '<span class="sk-avatar"></span>';
  else {
    if ( $padSkeletonShape === 'card' ) $padSkeletonHtml .= '<span class="sk-image"></span>';
    for ( $padSkeletonRow = 0; $padSkeletonRow < $padSkeletonLines; $padSkeletonRow++ ) {
      $padSkeletonHtml .= '<div class="sk-row">';
      for ( $padSkeletonCol = 0; $padSkeletonCol < ( $padSkeletonShape === 'table' ? $padSkeletonColumns : 1 ); $padSkeletonCol++ )
        $padSkeletonHtml .= '<span class="sk-line"></span>';
      $padSkeletonHtml .= '</div>';
    }
  }
  return padWidgetStyle ( 'skeleton', [], padWidgetAsset ( 'skeleton.css' ) ) . $padSkeletonHtml . '</div></div>';

?>
