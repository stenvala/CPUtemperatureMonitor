<?php

$file = 'minJS.json';
for ($ind = 1; $ind < count($argv); $ind++) {
  switch ($argv[$ind]) {
    case '-h':
      echo "minJS.php

Tool for minifying json files with yuicompressor.

Usage:
php minJS.php [options] [input file]

default for input file is minJS.json

Options
-h        Display this help then die
";
      die();
  }

  if ($ind == (count($argv) - 1)) {
    $file = $argv[$ind];
  }
}

$min = json_decode(file_get_contents($file), true);

$data = array();

if (array_key_exists('components', $min)) {
  foreach ($min['components'] as $key => $value) {
    echo "Loaded component: $key\n";
    $data = array_merge($data, loadFile($min['path'] . $key . '/' . $value));
  }
}
if (array_key_exists('files', $min)) {
  foreach ($min['files'] as $file) {
    $files = glob($file);
    foreach ($files as $f) {
      echo "Loaded file: $f\n";
      $data = array_merge($data, loadFile($f));
    }
  }
}

if (count($data) == 0) {
  echo "ERROR: no input files to minify";
}

saveFile($min['output'], $data);

$yuicompressor = '/Users/stenvala/bin/yuicompressor-2.4.7.jar';

$cmd = "java -jar $yuicompressor " .
  "{$min['output']} -o " .
  "{$min['output']} --charset utf-8";

shell_exec($cmd);

echo "Files minified successfully to '{$min['output']}' \n";

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
