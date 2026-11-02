<?php
  // review - Editorial Review with an explicit reviewed Thing, author and optional 1..5 rating.
  // Vocabulary: https://schema.org/Review . Ratings use a fixed 1..5 scale.
  return ['type'=>'Review','fields'=>[
    'name'=>['name','text',TRUE], 'body'=>['reviewBody','text',TRUE],
    'item'=>['itemReviewed.name','text',TRUE], 'itemurl'=>['itemReviewed.url','url'],
    'author'=>['author.name','text',TRUE], 'published'=>['datePublished','date'],
    'rating'=>['reviewRating.ratingValue',['number',[1,5]]], 'url'=>['url','url'],
  ],'objects'=>['itemReviewed'=>'Thing','author'=>'Person','reviewRating'=>'Rating']];
?>
