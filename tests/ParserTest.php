<?php

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testStr() {
      $this->assertTrue(
        Parser::str('abc')->run('abcdef')->equal(new Good('abc', 3))
      );
    }
}

