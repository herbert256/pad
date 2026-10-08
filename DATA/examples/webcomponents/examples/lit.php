<?php

  // The data of the timeline: a PHP array, written into the element's items attribute as JSON
  // by the ^ sigil. Lit reads the attribute into the items property (type: Array) and renders.

  $items = [ [ 'year' => 2019, 'what' => 'Custom elements and shadow DOM in every browser' ],
             [ 'year' => 2020, 'what' => 'Form-associated custom elements, ElementInternals' ],
             [ 'year' => 2023, 'what' => 'Declarative shadow DOM - shadowrootmode - in Chrome and Safari' ],
             [ 'year' => 2024, 'what' => 'Declarative shadow DOM in Firefox: everywhere' ] ];

?>
