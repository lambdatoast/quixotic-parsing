<?php

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testStrSuccesses() {
      $this->assertTrue(
        Parser::str('abc')->run('abcdef')->equal(new Good('abc', 3))
      );

      $this->assertTrue(
        Parser::product(
          Parser::str('ab'),
          Parser::str('cd')
        )->run('abcd')
         ->equal(new Good(array('ab', 'cd'), 2))
      );
    }
}

