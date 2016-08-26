<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\is_stringy;

class IsStringyTest extends TestCase {
  public function testBoolean() {
    $this->assertTrue(is_stringy(true));
  }

  public function testFloat() {
    $this->assertTrue(is_stringy(5.7));
  }

  public function testInt() {
    $this->assertTrue(is_stringy(3));
  }

  public function testString() {
    $this->assertTrue(is_stringy('this is a string!'));
  }

  public function testNull() {
    $this->assertTrue(is_stringy(null));
  }

  public function testArray() {
    $this->assertFalse(is_stringy(['this', 'is', 'an', 'array']));
  }

  public function testObjectWithoutToString() {
    $this->assertFalse(is_stringy(new \stdClass()));
  }

  public function testObjectWithToString() {
    $this->assertTrue(is_stringy($this));
  }

  public function testFunction() {
    // All anonymous functions should be represented as an instance of a Closure
    // so testing them explicitily is probably redundant, but hey...

    $this->assertFalse(is_stringy(function () {}));
  }

  public function testResource() {
    $fh = fopen('data:text/plain,stringystringstringstringstring', 'r');

    $this->assertFalse(is_stringy($fh));

    fclose($fh);
  }

  public function __toString() {
    return 'IsStringTest.php Test';
  }
}
