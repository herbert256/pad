<?php

  // Every icon of pad/subs/icons/types/ is drawn in the one style of the set: the file's svg
  // element is the same for all - the 24 by 24 grid, no fill, currentColor lines 2 wide with
  // round caps and joins - and inside it stand only path, circle, rect and ellipse elements
  // with their geometry: no fill, no colour, no style, no event attribute, no script, no
  // reference to another file. {icon:star} writes what the file holds, so an icon added with
  // anything else in it would carry that into every page. The names of the icons that are
  // not are listed, with what is wrong.

  $open  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"'
         . ' stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';

  $allow = [ 'path'    => [ 'd' ],
             'circle'  => [ 'cx', 'cy', 'r' ],
             'rect'    => [ 'x', 'y', 'width', 'height', 'rx' ],
             'ellipse' => [ 'cx', 'cy', 'rx', 'ry' ] ];

  $faults = [];

  foreach ( padIconNames () as $name ) {

    $svg = (string) file_get_contents ( PAD . "subs/icons/types/$name.svg" );

    if ( ! str_starts_with ( $svg, "$open\n" ) or ! str_ends_with ( $svg, "\n</svg>\n" ) ) {
      $faults [] = "$name: not the svg element of the set";
      continue;
    }

    $body = substr ( $svg, strlen ( $open ), - strlen ( "</svg>\n" ) );

    preg_match_all ( '#<([a-z]+)((?:\s+[a-z-]+="[^"]*")*)\s*/>#', $body, $elements, PREG_SET_ORDER );

    if ( trim ( preg_replace ( '#<([a-z]+)((?:\s+[a-z-]+="[^"]*")*)\s*/>#', '', $body ) ) !== '' )
      $faults [] = "$name: more than empty elements";

    foreach ( $elements as list ( , $element, $attributes ) ) {

      if ( ! isset ( $allow [$element] ) ) {
        $faults [] = "$name: a $element";
        continue;
      }

      preg_match_all ( '#([a-z-]+)="([^"]*)"#', $attributes, $pairs, PREG_SET_ORDER );

      foreach ( $pairs as list ( , $attribute, $value ) )
        if ( ! in_array ( $attribute, $allow [$element] ) )
          $faults [] = "$name: $attribute on a $element";
        elseif ( $attribute == 'd' ? ! preg_match ( '/^M[MmLlHhVvCcSsAaZz0-9 .-]+$/', $value ) : ! is_numeric ( $value ) )
          $faults [] = "$name: $attribute=\"$value\"";

    }

  }

  $verdict = $faults ? implode ( ' | ', $faults ) : 'every icon is lines on the grid';

?>
