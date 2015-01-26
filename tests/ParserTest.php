<?php

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testStrSuccesses() {
      $this->assertTrue(
        Parser::str('abc')->run('abcdef')->equal(new Good('abc', 3))
      );

      $this->assertEquals(
        Parser::product(
          Parser::str('ab'),
          Parser::str('cd')
        )->run('abcd'),
        new Good(array('ab', 'cd'), 4),
        'Parsing product'
      );

      $this->assertEquals(
        Parser::str('abc')->chain(function ($s) {
          return Parser::str(strtoupper($s));
        })->run('abcABC'), new Good('ABC', 6),
        'Contextual parsing'
      );
    }
}

