<?php

  // A gallery whose pictures open large over the page - the lightbox widget.
  //
  //   {lightbox label='The harbour'}
  //     {tab 'At dawn'}<img src="harbour/dawn.jpg" alt="Boats in the morning mist">{/tab}
  //     {tab 'At noon'}<img src="harbour/noon.jpg" alt="The quay in bright sun">{/tab}
  //   {/lightbox}
  //
  // Every {tab} is a thumbnail in a grid: a link to the view of its picture, an element of
  // its own id that the CSS shows over the whole window when the address names it (:target)
  // - the picture as large as the window allows, its caption with 'n / count' under it, a
  // link to the picture before and after it and a close link back to the thumbnail. So it
  // works without a script: the back button closes a view, and an address ending in the id
  // of a view opens it. The page behind does not scroll while a view is open (:has).
  //
  // A small script, once per page with the CSP nonce, makes it handier where it can run:
  // Escape closes the view, the arrow keys go to the picture before and after it, the focus
  // moves into the view as it opens and back to its thumbnail as it closes.
  //
  // The content of a tab is shown twice, small and large: an <img> or an <svg> - an image
  // the browser has once fetches once - with its own alt text, which names the thumbnail's
  // link where the tab has no label. A tab's label is the caption, and names the link.
  //
  // The items are collected the way {tabs} collects its tabs - widgets/_lib/tabs.php.
  //
  // padLightbox        the grid and the views
  // padLightboxStyle   the CSS, assets/lightbox.css, once per page
  // padLightboxScript  the script, assets/lightbox.js, once per page

  include_once PAD . 'widgets/_lib/tabs.php';

  function padLightbox ( $items, $label ) {

    $id    = padWidgetId ( 'lightbox', [ $label, array_column ( $items, 'label' ), count ( $items ) ] );
    $count = count ( $items );
    $grid  = $views = '';

    foreach ( $items as $index => $item ) {

      $n    = $index + 1;
      $prev = ( $n == 1 ) ? $count : $n - 1;
      $next = ( $n == $count ) ? 1 : $n + 1;
      $name = padWidgetAttr ( $item ['label'] );
      $says = "$n of $count" . ( $name !== '' ? ": $name" : '' );

      $open = ( $name !== '' ) ? " aria-label=\"$name, picture $n of $count\"" : '';

      $grid .= padProtect ( "<li><a class=\"pad-lightbox-thumb\" id=\"$id-$n-thumb\" href=\"#$id-$n\"$open>" )
             . $item ['content']
             . padProtect ( '</a></li>' );

      $views .= padProtect ( "<div class=\"pad-lightbox-view\" id=\"$id-$n\" role=\"dialog\" aria-label=\"Picture $says\">"
                           . "<a class=\"pad-lightbox-backdrop\" href=\"#$id-$n-thumb\" tabindex=\"-1\" aria-hidden=\"true\"></a>"
                           . '<figure class="pad-lightbox-figure">' )
              . $item ['content']
              . padProtect ( '<figcaption class="pad-lightbox-caption">' . $name
                           . "<span class=\"pad-lightbox-count\">$n / $count</span></figcaption></figure>"
                           . "<a class=\"pad-lightbox-close\" href=\"#$id-$n-thumb\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></a>"
                           . ( $count > 1 ? "<a class=\"pad-lightbox-prev\" href=\"#$id-$prev\" aria-label=\"Previous picture\"><span aria-hidden=\"true\">&#8249;</span></a>"
                                          . "<a class=\"pad-lightbox-next\" href=\"#$id-$next\" aria-label=\"Next picture\"><span aria-hidden=\"true\">&#8250;</span></a>" : '' )
                           . '</div>' );

    }

    return padLightboxStyle ()
         . padProtect ( "<div class=\"pad-lightbox\" id=\"$id\" role=\"group\" aria-label=\"" . padWidgetAttr ( $label ) . '">'
                      . '<ul class="pad-lightbox-grid">' )
         . $grid
         . padProtect ( '</ul>' )
         . $views
         . padProtect ( '</div>' )
         . padLightboxScript ();

  }

  function padLightboxStyle () {

    return padWidgetStyle ( 'lightbox',
      [ 'accent'   => [ '#2a78d6', '#5598e7' ],
        'shade'    => [ '#efeeea', '#2a2a28' ],
        'backdrop' => [ 'rgba(12,12,11,.9)', 'rgba(0,0,0,.92)' ],
        'button'   => [ 'rgba(255,255,255,.14)', 'rgba(255,255,255,.12)' ],
        'caption'  => [ '#f4f3ee', '#ecebe6' ] ],
      padWidgetAsset ( 'lightbox.css' ) );

  }

  function padLightboxScript () {

    return padWidgetScript ( 'lightbox', padWidgetAsset ( 'lightbox.js' ) );

  }

?>
