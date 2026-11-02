<?php

  // palette= - a chart of any kind drawn in the colours of a palette of subs/themes/palettes/types/:
  //
  //   {chart:bar data='sales', value='online, shop, phone', palette='okabe-ito'/}
  //   {chart:heatmap data='visits', x='hour', y='day', value='count', palette='viridis'/}
  //
  // The kinds draw as they always do - their marks carry the classes of the categorical slots,
  // pc-c1 to pc-c8 and pc-l1 to pc-l8, and of the heatmap's ramp, pc-h0 to pc-h6 - and the svg
  // gets a class of the palette and the number of slots it uses, pc-pal-viridis-3, whose rule
  // in the chart's own <style> gives it the palette's colours for those as custom properties,
  // which beat the defaults (subs/themes/palettes/_lib/palette.php, padPaletteChart): a categorical
  // palette its colours in its order, a sequential or diverging one spread over the slots the
  // chart uses - so three series of viridis are its purple, teal and green, not three
  // purples - and its whole length on the ramp. The slots a chart uses are read from what it
  // drew, the highest slot class in the SVG. A scale's dark end may be the ramp's first step,
  // where the default ramp has its lightest, so the value a heatmap writes on a cell takes the
  // ink of its step, black or white (--pad-chart-heat-ink-0 to -6), by rules of the same
  // class. The colours stood in a style attribute of the svg, which the style-src nonce the
  // engine supports drops - a nonce covers no attribute - and a chart in a palette was drawn
  // in the default colours under it. A chart drawn in the browser gets the same: the server
  // hands it the class and the rules for each number of slots, and www/assets/charts/palette.js puts on
  // the ones the drawing needs - the same SVG, character for character. Without palette=
  // nothing of this runs and a chart is drawn as before.
  //
  //   padChartPaletteName   the palette the chart is given - '' for none, a name that is no
  //                         palette named under the strict check
  //   padChartPaletteApply  the svg of a chart with the palette's class and rules
  //   padChartPaletteRule   the class and the rules of a palette for so many slots
  //   padChartPaletteSlots  the number of categorical slots an SVG uses, 1 at least
  //   padChartPaletteStyles the class and the rules for 1 to 8 slots, for the browser

  function padChartPaletteName () {

    global $padCheckSyntax;

    $name = padTagParm ( 'palette', '' );

    if ( $name === '' or $name === NULL or $name === FALSE )
      return '';

    $name = is_scalar ( $name ) ? strtolower ( trim ( padParmText ( (string) $name ) ) ) : '';

    include_once PAD . 'subs/themes/palettes/_lib/palette.php';

    if ( padPaletteType ( $name ) )
      return padChartPaletteValidate ( $name ) ? $name : '';

    if ( $padCheckSyntax )
      padError ( "the chart has no palette '" . padMakeSafe ( $name, 20 ) . "' - " . implode ( ', ', padPaletteTypes () ) );

    return '';

  }

  function padChartPaletteApply ( $svg, $name, $intent = NULL ) {

    if ( $name === '' or ! is_string ( $svg ) or $svg === '' )
      return $svg;

    $slots = padChartPaletteSlots ( $svg );
    if ( ! padChartPaletteValidate ( $name, $slots, $intent ) ) return '';
    [ $class, $rules ] = padChartPaletteRule ( $name, $slots );

    return preg_replace_callback ( '/(<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" class="pad-chart [^"]*)("[^>]*>[\s\S]*?)(<\/style>)/',
                                   fn ( $m ) => $m [1] . " $class" . $m [2] . $rules . $m [3], $svg );

  }

  // Explicit intended scale. Omitted usage keeps the established palette sampling;
  // scheme checks metadata's named context and never silently swaps colors.
  function padChartPaletteIntent ( $name ) {

    $usage = padTagParm ( 'usage', '' );
    $scheme = padTagParm ( 'scheme', 'light' );
    $meta = padPaletteMetadata ( $name );
    return [ 'usage' => $usage, 'scheme' => $scheme,
             'type' => $meta ['type'] ?? '',
             'recommendedCategories' => $meta ['recommendedCategories'] ?? NULL ];

  }

  function padChartPaletteValidate ( $name, $categories = 0, $intent = NULL ) {

    $intent ??= padChartPaletteIntent ( $name );
    $usage = $intent ['usage'];
    $scheme = $intent ['scheme'];
    if ( $usage === '' ) {
      $issues = in_array ( $scheme, [ 'light', 'dark' ], TRUE ) ? [] : [ 'Scheme must be light or dark' ];
    } elseif ( ! is_string ( $usage ) or ! is_string ( $scheme ) ) {
      $issues = [ 'usage and scheme must be text' ];
    } else {
      $issues = padPaletteUsage ( $name, $usage, $usage === 'categorical' ? $categories : 0, $scheme );
    }
    if ( ! $issues ) return TRUE;
    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( 'the chart palette: ' . implode ( '; ', $issues ) );
    return FALSE;

  }

  // The class of a palette for so many slots - pc-pal-viridis-3 - and its rules: the palette's
  // custom properties on the svg of that class and, for a scale, the ink of a value on each
  // step of the ramp - light ones (pc-in) on the dark end of the default ramp, its text colour
  // on the rest.

  function padChartPaletteRule ( $name, $n ) {

    $class = "pc-pal-$name-$n";
    $style = padPaletteChart ( $name, $n );
    $rules = ".pad-chart.$class{" . $style . '}';

    if ( str_contains ( $style, '--pad-chart-heat-ink-' ) )
      for ( $step = 0; $step <= 6; $step++ )
        $rules .= ".pad-chart.$class .pc-gap.pc-h$step+text{fill:var(--pad-chart-heat-ink-$step);stroke:none}";

    return [ $class, $rules ];

  }

  function padChartPaletteSlots ( $svg ) {

    preg_match_all ( '/class="(?:[^"]*\s)?pc-[cl]([1-8])(?=[\s"])/', $svg, $found );

    return $found [1] ? max ( array_map ( 'intval', $found [1] ) ) : 1;

  }

  function padChartPaletteStyles ( $name ) {

    $styles = [];

    for ( $n = 1; $n <= 8; $n++ )
      $styles [] = padChartPaletteRule ( $name, $n );

    return $styles;

  }

?>
