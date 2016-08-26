<?php

list(, $file) = count($argv) > 1 ? $argv : [null, 'php://stdin'];

if ($file !== 'php://stdin' and !is_readable($file)) {
  file_put_contents('php://stderr', "Couldn't open '$file' for reading!" . PHP_EOL);
  die(404); // heh
}

if ($fh = fopen($file, 'r')) {
  register_shutdown_function(function () use ($fh) {
    fclose($fh);
  });
}

$mostest = [
  'word'        => '',
  'letter'      => '',
  'occurrences' => 0,
];

while ($line = rtrim(fgets($fh), "\r\n")) {
  $words = preg_split('/\p{Zs}+/', $line);

  foreach ($words as $word) {
    $chars = [];
//str_split($word)
    foreach (preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) as $char) {
      array_key_exists($char, $chars) ? $chars[$char]++ : $chars[$char] = 1;

      if ($chars[$char] > $mostest['occurrences']) {
        $mostest['word']        = $word;
        $mostest['letter']      = $char;
        $mostest['occurrences'] = $chars[$char];
      }
    }
  }
}

echo "The word '{$mostest['word']}' has the mostest occurrences ({$mostest['occurrences']}) of a single character ('{$mostest['letter']}')", PHP_EOL;
