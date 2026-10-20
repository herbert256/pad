<?php

  // Build strategy 'fixed' for narcissistic: the numbers that are the sum of their own digits
  // each raised to the number of digits - 153 = 1^3 + 5^3 + 3^3 - which are 88 in all, the
  // largest of 39 digits. These are the 50 inside the integer range, found by trying every
  // multiset of digits of each length up to 19, so the list is all of them that a PHP integer
  // holds and the index of it settles {keep narcissistic} and its friends.

  return [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 153, 370, 371, 407, 1634, 8208, 9474, 54748, 92727, 93084,
           548834, 1741725, 4210818, 9800817, 9926315, 24678050, 24678051, 88593477, 146511208,
           472335975, 534494836, 912985153, 4679307774, 32164049650, 32164049651, 40028394225,
           42678290603, 44708635679, 49388550606, 82693916578, 94204591914, 28116440335967,
           4338281769391370, 4338281769391371, 21897142587612075, 35641594208964132,
           35875699062250035, 1517841543307505039, 3289582984443187032, 4498128791164624869,
           4929273885928088826 ];

?>
