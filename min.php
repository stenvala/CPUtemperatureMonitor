<?php

$min = json_decode(file_get_contents('min.json'),true);

$data = array();

foreach ($min['components'] as $key => $value) {
  echo "Loaded component: $key\n";
  $data = array_merge($data, loadFile($min['path'] . $key . '/' . $value));
}

saveFile($min['file'], $data);

$yuicompressor = '/Users/stenvala/bin/yuicompressor-2.4.7.jar';

$cmd = "java -jar $yuicompressor " .
        "{$min['file']} -o " .
        "{$min['file']} --charset utf-8";

shell_exec($cmd);

echo "Files minified successfully to '{$min['file']}' \n";

function saveFile($fname, $lines) {
  $handle = fopen($fname, 'w');
  foreach ($lines as $line) {
    fwrite($handle, $line . PHP_EOL);
  }
  fclose($handle);
}

function loadFile($fname) {
  $handle = @fopen($fname, 'r');
  if ($handle === false) {
    echo "Error: file $fname does not exist\n";
    die();
  }
  $con = array();
  while (($buffer = fgets($handle, 4096)) !== false) {
    array_push($con, rtrim(str_replace('  ', ' ', $buffer)));
  }
  if (!feof($handle)) {
    echo "Error: unexpected fgets() fail file: $fname\n";
    die();
  }
  fclose($handle);
  return $con;
}

?>