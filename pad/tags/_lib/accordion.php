<?php

  // An accordion without JavaScript - the {accordion} tag: every {tab} an HTML <details>
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
  // The items are collected the way {tabs} collects its tabs - lib/tabs.php.

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
      '.pad-accordion{margin:0 0 1em;border-top:1px solid var(--pad-accordion-line)}'
      . '.pad-accordion-item{border-bottom:1px solid var(--pad-accordion-line)}'
      . '.pad-accordion-summary{display:flex;align-items:center;gap:.75em;padding:.8em .5em;cursor:pointer;list-style:none;'
      .   'color:var(--pad-accordion-text);font-weight:600}'
      . '.pad-accordion-summary::-webkit-details-marker{display:none}'
      . '.pad-accordion-summary::after{content:"";flex:none;margin-left:auto;width:.5em;height:.5em;'
      .   'border-right:2px solid var(--pad-accordion-muted);border-bottom:2px solid var(--pad-accordion-muted);'
      .   'transform:translateY(-25%) rotate(45deg);transition:transform .15s}'
      . '.pad-accordion-item[open]>.pad-accordion-summary::after{transform:translateY(25%) rotate(-135deg)}'
      . '.pad-accordion-summary:hover{background:var(--pad-accordion-hover)}'
      . '.pad-accordion-summary:focus-visible{outline:2px solid var(--pad-accordion-accent);outline-offset:-2px;border-radius:6px}'
      . '.pad-accordion-body{padding:0 .5em 1em;color:var(--pad-accordion-text)}'
      . '.pad-accordion-body>:first-child{margin-top:0}.pad-accordion-body>:last-child{margin-bottom:0}'
      . '@media (prefers-reduced-motion:reduce){.pad-accordion-summary::after{transition:none}}' );

  }

?>
