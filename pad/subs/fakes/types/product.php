<?php

  // product - The name of a product in a shop: a word for its look, what it is made of and
  // what it is, Rustic Oak Stool or Compact Enamel Mug - each thing only of the materials it
  // is made of, so a glass blanket never turns up. A modest list, for a prototype's shop, its
  // price beside it from price and its number from ean.

  $things = [
    'Backpack'      => [ 'Canvas', 'Leather', 'Nylon' ],
    'Basket'        => [ 'Rattan', 'Seagrass', 'Willow' ],
    'Blanket'       => [ 'Cotton', 'Fleece', 'Linen', 'Wool' ],
    'Bottle'        => [ 'Glass', 'Steel' ],
    'Bowl'          => [ 'Bamboo', 'Ceramic', 'Enamel', 'Walnut' ],
    'Candle Holder' => [ 'Brass', 'Ceramic', 'Glass' ],
    'Chair'         => [ 'Bamboo', 'Oak', 'Rattan', 'Steel' ],
    'Clock'         => [ 'Brass', 'Oak', 'Steel' ],
    'Cushion'       => [ 'Cotton', 'Linen', 'Velvet', 'Wool' ],
    'Jacket'        => [ 'Cotton', 'Denim', 'Leather', 'Wool' ],
    'Lamp'          => [ 'Brass', 'Ceramic', 'Glass', 'Steel' ],
    'Mug'           => [ 'Ceramic', 'Enamel', 'Glass', 'Stoneware' ],
    'Notebook'      => [ 'Leather', 'Linen', 'Paper' ],
    'Scarf'         => [ 'Cotton', 'Linen', 'Silk', 'Wool' ],
    'Shelf'         => [ 'Bamboo', 'Oak', 'Pine', 'Steel' ],
    'Stool'         => [ 'Bamboo', 'Oak', 'Pine', 'Steel' ],
    'Table'         => [ 'Glass', 'Oak', 'Steel', 'Walnut' ],
    'Teapot'        => [ 'Cast Iron', 'Ceramic', 'Glass' ],
    'Vase'          => [ 'Ceramic', 'Glass', 'Stoneware' ],
    'Wallet'        => [ 'Canvas', 'Leather' ],
  ];

  $looks = [ 'Classic', 'Compact', 'Essential', 'Everyday', 'Handmade', 'Large', 'Minimal', 'Modern',
             'Rustic', 'Slim', 'Small', 'Vintage' ];

  $thing = padFakePick ( array_keys ( $things ) );

  return padFakePick ( $looks ) . ' ' . padFakePick ( $things [$thing] ) . " $thing";

?>
