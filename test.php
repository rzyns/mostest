<?php
include('mostest.php');

// $str = 'ę: dziękuje bardzo, pan Dziurzyński! A przepraszam, czy możesz powiedzieć, jak się mówi po polsku "I eat food"?';

// $fh = fopen('test.txt', 'r');

// while (!feof($fh)) {
//   echo get_next_utf8_character($fh);
// }

// fclose($fh);

$fh = fopen($argv[1], 'r');
register_shutdown_function(function () use ($fh) {
  if (!is_resource($fh)) {
    echo '$fh is not a resource!', PHP_EOL;
    return;
  }
  var_dump(fclose($fh));
});

while (!feof($fh)) {
  $c = mostest\get_next_utf8_character($fh);

  if (is_null($c)) break;

  if ($c === false) {
    $hex = strtoupper(bin2hex(fgetc($fh)));
    fwrite(STDERR, sprintf('Invalid UTF-8 byte at %d: "0x%s"', ftell($fh), $hex) . PHP_EOL);
    continue;
  }

  printf('%d : %s : %d' . PHP_EOL, ftell($fh), ord($c) === 10 ? json_decode('"\u240a"') : $c, strlen($c));
}

rewind($fh);
var_dump(mostest\get_mostest($fh));
