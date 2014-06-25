<?php

// CSS wrapper
// (c) Antti Stenvall
// antti@stenvall.fi
//

header("Content-type: text/css; charset=utf-8");

$data = file_get_contents('style.css');

// allows #(var) variables in css
$var = array(
  'red' => 'rgb(235,39,123)'
);

$mapper = function($val){
  return "#($val)";
};

print str_replace(array_map($mapper,array_keys($var)),array_values($var),$data);

?>
