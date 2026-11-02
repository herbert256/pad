<?php

  include_once PAD . 'subs/widgets/_lib/progress.php';

  function padProgressRing ( $value, $max, $label, $color, $size ) {
    $value = max ( 0, min ( (float) $max, (float) $value ) );
    $ratio = $value / $max;
    $percent = $ratio <= 0 ? 0 : ( $ratio >= 1 ? 100 : max ( 1, min ( 99, (int) round ( $ratio * 100 ) ) ) );
    $width = [ 'small' => 64, 'medium' => 96, 'large' => 128 ] [$size];
    $label = trim ( $label ) ?: 'Progress';
    $colors = [ 'accent' => '#2764d9', 'success' => '#208545', 'warning' => '#a76408', 'danger' => '#c93644', 'neutral' => '#707078' ];
    $arc = padProgressNumber ( $ratio * 100 );
    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-progress-ring" role="progressbar" aria-label="' . padProgressAttr ( $label )
      . '" aria-valuemin="0" aria-valuemax="' . padProgressNumber ( $max ) . '" aria-valuenow="' . padProgressNumber ( $value )
      . '" width="' . $width . '" height="' . $width . '" viewBox="0 0 100 100">'
      . '<title>' . padProgressAttr ( $label ) . ': ' . $percent . '%</title>'
      . '<circle cx="50" cy="50" r="42" fill="none" stroke="currentColor" opacity=".15" stroke-width="8"/>'
      . '<circle cx="50" cy="50" r="42" fill="none" stroke="' . $colors [$color] . '" stroke-width="8" pathLength="100" stroke-dasharray="' . $arc . ' 100" transform="rotate(-90 50 50)"/>'
      . '<text x="50" y="50" dy=".35em" text-anchor="middle" fill="currentColor" font-size="20" font-family="system-ui">' . $percent . '%</text></svg>';
  }

?>
