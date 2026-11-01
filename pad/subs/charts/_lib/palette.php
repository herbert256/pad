<?php

  // palette= - a chart of any kind drawn in the colours of a palette of subs/palettes/types/:
  //
  //   {chart 'bar', data='sales', value='online, shop, phone', palette='okabe-ito'/}
  //   {chart 'heatmap', data='visits', x='hour', y='day', value='count', palette='viridis'/}
  //
  // The kinds draw as they always do - their marks carry the classes of the categorical slots,
  // pc-c1 to pc-c8 and pc-l1 to pc-l8, and of the heatmap's ramp, pc-h0 to pc-h6 - and the svg
  // gets the palette's colours for those as custom properties on its own style attribute,
  // which beat the defaults (subs/palettes/_lib/palette.php, padPaletteChart): a categorical
  // palette its colours in its order, a sequential or diverging one spread over the slots the
  // chart uses - so three series of viridis are its purple, teal and green, not three
  // purples - and its whole length on the ramp. The slots a chart uses are read from what it
  // drew, the highest slot class in the SVG. A scale's dark end may be the ramp's first step,
  // where the default ramp has its lightest, so the value a heatmap writes on a cell takes the
  // ink of its step, black or white (--pad-chart-heat-ink-0 to -6): rules for that go into the
  // chart's own <style>, for an svg whose style attribute carries the ink alone. A
  // chart drawn in the browser gets the same: the server hands it the eight possible
  // attributes, one for each number of slots, and client/palette.js puts on the one the
  // drawing needs, and the rules - the same SVG, character for character. Without palette=
  // nothing of this runs and a chart is drawn as before.
  //
  //   padChartPaletteName   the palette the chart is given - '' for none, a name that is no
  //                         palette named under the strict check
  //   padChartPaletteApply  the svg of a chart with the palette's properties on it
  //   padChartPaletteSlots  the number of categorical slots an SVG uses, 1 at least
  //   padChartPaletteStyles the style attribute for 1 to 8 slots, for the browser

  function padChartPaletteName () {

    global $padCheckSyntax;

    $name = padTagParm ( 'palette', '' );

    if ( $name === '' or $name === NULL or $name === FALSE )
      return '';

    $name = is_scalar ( $name ) ? strtolower ( trim ( padParmText ( (string) $name ) ) ) : '';

    include_once PAD . 'subs/palettes/_lib/palette.php';

    if ( padPaletteType ( $name ) )
      return $name;

    if ( $padCheckSyntax )
      padError ( "the chart has no palette '" . padMakeSafe ( $name, 20 ) . "' - " . implode ( ', ', padPaletteTypes () ) );

    return '';

  }

  function padChartPaletteApply ( $svg, $name ) {

    if ( $name === '' or ! is_string ( $svg ) or $svg === '' )
      return $svg;

    $style = padPaletteChart ( $name, padChartPaletteSlots ( $svg ) );
    $rules = str_contains ( $style, '--pad-chart-heat-ink-' ) ? padChartPaletteRules () : '';

    return preg_replace_callback ( '/(<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" class="pad-chart [^"]*")([^>]*>[\s\S]*?)(<\/style>)/',
                                   fn ( $m ) => $m [1] . ' style="' . $style . '"' . $m [2] . $rules . $m [3], $svg );

  }

  // The ink of a value on a step of the ramp - the heatmap writes one on every cell, light
  // ones (pc-in) on the dark end of the default ramp, its text colour on the rest.

  function padChartPaletteRules () {

    $rules = '';

    for ( $step = 0; $step <= 6; $step++ )
      $rules .= ".pad-chart[style*=heat-ink] .pc-gap.pc-h$step+text{fill:var(--pad-chart-heat-ink-$step);stroke:none}";

    return $rules;

  }

  function padChartPaletteSlots ( $svg ) {

    preg_match_all ( '/class="(?:[^"]*\s)?pc-[cl]([1-8])(?=[\s"])/', $svg, $found );

    return $found [1] ? max ( array_map ( 'intval', $found [1] ) ) : 1;

  }

  function padChartPaletteStyles ( $name ) {

    $styles = [];

    for ( $n = 1; $n <= 8; $n++ )
      $styles [] = padPaletteChart ( $name, $n );

    return $styles;

  }

?>
