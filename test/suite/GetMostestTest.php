<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\get_mostest;

class GetMostestTest extends TestCase {
  /**
   * @dataProvider stringProvider
   */
  public function testStrings($string, $expected) {
    $this->assertEquals($expected, get_mostest($string)['word']);

  //   $this->assertArraySubset([
  //     'word' => $expected,
  //   ], $actual);
  }

  /**
   * @dataProvider stringProvider
   */
  public function testFiles($string, $expected) {
    $fh = fopen('php://memory', 'w+');
    fwrite($fh, $string);
    rewind($fh);

    $this->assertEquals($expected, get_mostest($fh)['word']);

    fclose($fh);
  }

  public function stringProvider() {
    return [
      ['“O Romeo, Romeo, wherefore art thou Romeo?”', 'wherefore'],
      ['“O Romeo, RomEo, whErEfore art thou Romeo?”', 'whErEfore'],
      ['“Some people feel the rain, while others just get wet.”', 'people'],
      ['“Some pEople feel the rain, while others just get wet.”', 'pEople'],
      ["I just caaaaaan't get enough of this stuff!", "caaaaaan't"],
      ['This is some text with an invalid UTF-8 byte, "' . chr(254) . '", in the middle!', 'text'],
      ['Dziękuje uprzejmie, pan Dziurzyński! To jest bardzo ładny!', 'uprzejmie'],
      [chr(0xF0) . chr(0x9D) . chr(0x9C) . chr(0x89) . 'i! ńń', 'ńń'],
      ['', ''],
    ];
  }
}
