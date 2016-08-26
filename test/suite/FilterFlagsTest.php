<?php
namespace mostest\Test\Suite;

use mostest\Test\TestCase;

use function mostest\cli\filter_flags;

class FilterFlagsTest extends TestCase {
  static $original_argv;

  public static function setupBeforeClass() {
    global $argv;

    static::$original_argv = $argv;
  }

  public function tearDown() {
    global $argv;

    $this->assertEquals(static::$original_argv, $argv,
      'the global $argv should always remain unchanged');
  }

  public function testOperands() {
    $this->assertEquals(
      ['foo', 'bar', 'baz'],
      filter_flags(['foo', 'bar', 'baz'], '', [])
    );
  }

  public function testShort() {
    $this->assertEquals(
      ['arg1'],
      filter_flags(['-f', '-z', 'arg1', '-y', '-q'])
    );
  }

  public function testLong() {
    $this->assertEquals(
      ['bar.php', 'file.ext', 'foo.txt', '--', '-n', '--force', 'file2.ext'],
      filter_flags(['bar.php', '--debug', '-f', 'file.ext', '--verbose', 'foo.txt', '--', '-n', '--force', 'file2.ext'])
    );
  }
}
