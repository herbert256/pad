<?php

  // A wizard without JavaScript - the wizard widget: every {pad:tab} a step, one shown at a time.
  //
  //   {pad:form 'order'}
  //     {widget:wizard}
  //       {pad:tab 'Address'}{pad:input 'street', label='Street'/}...{/pad:tab}
  //       {pad:tab 'Delivery'}...{/pad:tab}
  //       {pad:tab 'Payment'}...<button>Order</button>{/pad:tab}
  //     {/widget:wizard}
  //   {/pad:form}
  //
  // Built as {widget:tabs} is: a radio button and a label per step, the panels behind them, the CSS
  // showing the panel whose radio is checked. The labels are the row of steps above the
  // panels - a number in a circle and the step's name - and the CSS marks the current step
  // and the ones before it (:has), with the line between them. The radios are one group: one
  // stop in the tab order, the arrow keys move between the steps, a screen reader hears
  // 'Payment, step 3 of 3'. Each panel is a region named by its step.
  //
  // Under every panel a Back and a Next: without scripting labels of the radio of the step
  // before and after it, which a click checks; with scripting - @media (scripting) decides,
  // as for {widget:modal} - real buttons, so the keyboard reaches them, and a small script, once per
  // page with the CSP nonce, checks the step's radio and moves the focus to it. The first
  // step has no Back, the last no Next: its own content ends it - a submit button. The steps
  // of a form are all in the form, shown or not, so every field is posted; a required field
  // on a step not shown stops the post, which a browser reports at a field the visitor cannot
  // see - the rules of a step are better checked by the page (padValidate). Printed, every
  // step shows.
  //
  // The items are collected the way {widget:tabs} collects its tabs - subs/widgets/_lib/tabs.php.
  //
  // padWizard        the steps, the panels and their buttons
  // padWizardStyle   the CSS, www/assets/widgets/wizard.css with the panels of twelve steps, once per
  //                  page; a wizard of more adds the rules of its further steps, each once
  // padWizardScript  the script, www/assets/widgets/wizard.js, once per page

  include_once PAD . 'subs/widgets/_lib/tabs.php';

  function padWizard ( $items, $active, $label, $next, $back ) {

    $id    = padWidgetId ( 'wizard', array_column ( $items, 'label' ) );
    $count = count ( $items );
    $next  = padWidgetAttr ( $next );
    $back  = padWidgetAttr ( $back );
    $steps = $panels = '';

    if ( $active < 1 )
      $active = 1;

    foreach ( $items as $index => $item ) {

      $n     = $index + 1;
      $check = ( $n == $active ) ? ' checked' : '';
      $nav   = '';

      $steps .= padProtect ( "<input class=\"pad-wizard-radio\" type=\"radio\" name=\"$id\" id=\"$id-$n\" aria-controls=\"$id-$n-panel\"$check>"
                           . "<label class=\"pad-wizard-step\" for=\"$id-$n\" id=\"$id-$n-label\">"
                           . "<span class=\"pad-wizard-number\" aria-hidden=\"true\">$n</span>"
                           . '<span class="pad-wizard-name">' . padWidgetAttr ( $item ['label'] ) . '</span>'
                           . "<span class=\"pad-wizard-of\">, step $n of $count</span></label>" );

      if ( $n > 1 )
        $nav .= "<label class=\"pad-wizard-back\" for=\"$id-" . ( $n - 1 ) . "\">$back</label>"
              . "<button type=\"button\" class=\"pad-wizard-back\" data-pad-step=\"$id-" . ( $n - 1 ) . "\">$back</button>";

      if ( $n < $count )
        $nav .= "<label class=\"pad-wizard-next\" for=\"$id-" . ( $n + 1 ) . "\">$next</label>"
              . "<button type=\"button\" class=\"pad-wizard-next\" data-pad-step=\"$id-" . ( $n + 1 ) . "\">$next</button>";

      $panels .= padProtect ( "<div class=\"pad-wizard-panel\" id=\"$id-$n-panel\" role=\"region\" aria-labelledby=\"$id-$n-label\">" )
               . $item ['content']
               . padProtect ( ( $nav !== '' ? "<div class=\"pad-wizard-nav\">$nav</div>" : '' ) . '</div>' );

    }

    return padWizardStyle ( $count )
         . padProtect ( "<div class=\"pad-wizard\" id=\"$id\" role=\"group\" aria-label=\"" . padWidgetAttr ( $label ) . '">' )
         . $steps . $panels
         . '</div>'
         . padWizardScript ();

  }

  function padWizardStyle ( $count ) {

    $css = padWidgetStyle ( 'wizard',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'on'      => [ '#ffffff', '#0d1b2e' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'line'    => [ '#d9d8d3', '#3f3f3c' ],
        'surface' => [ '#ffffff', '#1f1f1d' ] ],
      padWidgetAsset ( 'wizard.css' ) );

    for ( $n = 13; $n <= $count; $n++ )
      if ( padWidgetOnce ( "wizard-slot:$n" ) )
        $css .= padProtect ( '<style' . padStyleNonce () . '>' . padWizardSlot ( $n ) . '</style>' );

    return $css;

  }

  function padWizardSlot ( $n ) {

    return ".pad-wizard>.pad-wizard-radio:nth-of-type($n):checked~.pad-wizard-panel:nth-of-type($n){display:block}";

  }

  function padWizardScript () {

    return padWidgetScript ( 'wizard', padWidgetAsset ( 'wizard.js' ) );

  }

?>
