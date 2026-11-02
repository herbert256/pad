<?php

  // A short tip on hover and on focus - the tooltip widget.
  //
  //   {widget:tooltip 'Billed every month, cancel any time'}Starter plan{/widget:tooltip}
  //   VAT {widget:tooltip 'Value added tax, 21% on most goods'/}
  //
  // The trigger and the tip stand in one inline box, and the CSS shows the tip while the box
  // is hovered or holds the focus - no script. The trigger is focusable - a tabindex on a
  // text, nothing added to content that holds a link, a button or a field already, which take
  // the focus themselves - and names the tip with aria-describedby, the tip being a tooltip
  // role: a screen reader reads it after the trigger. The single tag's trigger is a small
  // button with an i, named 'More information'.
  //
  // A tip stays while the pointer moves onto it - a bridge spans the gap - so it can be read
  // and selected, and Escape hides it: the one thing the script does, written once per page
  // with the CSP nonce (WCAG 1.4.13, content on hover or focus that can be dismissed, hovered
  // and stays). Printed, no tip shows.
  //
  // A tooltip stands in a line of text, so its style and script follow it rather than lead
  // it: PHP's tidy ($padTidy) takes a <style> out of the body into the head, and the space
  // in front of one went with it - 'The {widget:tooltip ...}plan' read 'Theplan'. A <p> it stands
  // in tidy still ends at that <style>; a tip in a <div>, a list or a cell keeps its line.
  //
  // padTooltip        the trigger and its tip
  // padTooltipStyle   the CSS, www/assets/widgets/tooltip.css, once per page
  // padTooltipScript  the script, www/assets/widgets/tooltip.js, once per page

  function padTooltip ( $tip, $trigger, $place ) {

    $id   = padWidgetId ( 'tooltip', [ $tip, $trigger ] );
    $tip  = padWidgetAttr ( $tip );
    $tabs = preg_match ( '/<(a|button|input|select|textarea|summary)\b/i', $trigger ) ? '' : ' tabindex="0"';

    if ( $trigger === '' )
      $open = "<button type=\"button\" class=\"pad-tooltip-trigger pad-tooltip-info\" aria-describedby=\"$id\" aria-label=\"More information\">"
            . '<span aria-hidden="true">i</span></button>';
    else
      $open = "<span class=\"pad-tooltip-trigger\"$tabs aria-describedby=\"$id\">";

    return padProtect ( "<span class=\"pad-tooltip pad-tooltip-$place\">$open" )
         . $trigger
         . padProtect ( ( $trigger === '' ? '' : '</span>' )
                      . "<span class=\"pad-tooltip-tip\" role=\"tooltip\" id=\"$id\">$tip</span></span>" )
         . padTooltipStyle ()
         . padTooltipScript ();

  }

  function padTooltipStyle () {

    return padWidgetStyle ( 'tooltip',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'surface' => [ '#1f1f1d', '#ecebe6' ],
        'on'      => [ '#ffffff', '#1f1f1d' ] ],
      padWidgetAsset ( 'tooltip.css' ) );

  }

  function padTooltipScript () {

    return padWidgetScript ( 'tooltip', padWidgetAsset ( 'tooltip.js' ) );

  }

?>
