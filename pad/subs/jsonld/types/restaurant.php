<?php

  // {jsonld:restaurant data='restaurant'/} or {jsonld:restaurant name='De Ketel',
  // cuisine='Dutch', menu='?menu', street='Oudegracht 1', city='Utrecht', country='NL',
  // hours='Tu-Su 17:00-23:00', prices='$$'/} - a Restaurant: a local business that serves
  // food and drink, and what it serves.
  //
  // Every field of the business kind - its name, address, place on the map, opening hours
  // and prices - and two of a FoodEstablishment beside them, which a business of another
  // type cannot have: cuisine (servesCuisine, Italian, Dutch - a text with a comma between
  // them for more) and menu (menu, the address of its menu - the property Google's local
  // business documentation names, which Schema.org calls hasMenu as well). type= is
  // Restaurant when left out, or another food establishment: Bakery, BarOrPub, Brewery,
  // CafeOrCoffeeShop, Distillery, FastFoodRestaurant, IceCreamShop, Winery or
  // FoodEstablishment itself - the types of schema.org V30.1 under FoodEstablishment.

  $spec = include PAD . 'subs/jsonld/types/business.php';

  $spec ['type'] = 'Restaurant';

  $spec ['fields'] ['type'] [1] [1] = [ 'Restaurant', 'Bakery', 'BarOrPub', 'Brewery', 'CafeOrCoffeeShop', 'Distillery',
                                        'FastFoodRestaurant', 'FoodEstablishment', 'IceCreamShop', 'Winery' ];

  $spec ['fields'] ['cuisine'] = [ 'servesCuisine', 'text' ];
  $spec ['fields'] ['menu']    = [ 'menu',          'url'  ];

  return $spec;

?>
