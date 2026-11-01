<?php

  // An accordion without JavaScript - the accordion widget: every {tab} an HTML <details>
  // with its label as the <summary>, so the browser opens and closes it, from the keyboard
  // too, and a reader hears 'collapsed' or 'expanded'. A find in the page opens the item it
  // finds in, and printed what is open shows.
  //
  //   {accordion single, open=1}
  //     {tab 'Can I return it?'}Within 30 days ...{/tab}
  //     {tab 'Is there a warranty?'}Two years ...{/tab}
  //   {/accordion}
  //
  // single gives the items one name - the HTML exclusive accordion: opening one closes the
  // one that was open. open= names the item open at first, by its number from 1 or its
  // label; open without a value opens them all, which a single accordion cannot.
  //
  // The items are collected the way {tabs} collects its tabs - subs/widgets/_lib/tabs.php.

  include_once PAD . 'subs/widgets/_lib/tabs.php';

  function padAccordion ( $items, $single, $open ) {

    $id   = padWidgetId ( 'accordion', array_column ( $items, 'label' ) );
    $name = $single ? " name=\"$id\"" : '';
    $out  = '';

    foreach ( $items as $index => $item ) {

      $isOpen = ( $open === TRUE or $open == $index + 1 ) ? ' open' : '';

      $out .= padProtect ( "<details class=\"pad-accordion-item\"$name$isOpen>"
                         . '<summary class="pad-accordion-summary">' . padWidgetAttr ( $item ['label'] ) . '</summary>'
                         . '<div class="pad-accordion-body">' )
            . $item ['content']
            . '</div></details>';

    }

    return padAccordionStyle ()
         . padProtect ( "<div class=\"pad-accordion\" id=\"$id\">" )
         . $out
         . '</div>';

  }

  function padAccordionStyle () {

    return padWidgetStyle ( 'accordion',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'line'    => [ '#e4e3df', '#3a3a37' ],
        'hover'   => [ '#f4f3f0', '#242422' ] ],
      padWidgetAsset ( 'accordion.css' ) );

  }

?>
