<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\is_valid_continuation;

class IsValidContinuationTest extends TestCase {
  public function testAsciiCharacters() {
    for ($c = 0; $c < 128; $c++) {
      $this->assertFalse(is_valid_continuation(chr($c)));
    }
  }

  public function testValidContinuations() {
    for ($c = 128; $c < 192; $c++) {
      $this->assertTrue(is_valid_continuation(chr($c)), sprintf('0b%d (%d) should be a valid continuation', $c, decbin($c)));
    }
  }

  public function testBadLength() {
    $this->assertFalse(is_valid_continuation('asdfgh'));
  }
}
