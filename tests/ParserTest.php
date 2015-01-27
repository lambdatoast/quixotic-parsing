<?php

class ParserTest extends \PHPUnit_Framework_TestCase {

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

      $this->assertEquals(
        Parser::times(3, Parser::str('ab'))->run('ababab'),
        new Good(array('ab', 'ab', 'ab'), 6),
        'Parser::times(n, p) when successful must produce list of n results.'
      );

      $count = function ($xs) { return count($xs); };

      $this->assertEquals(
        Parser::times(3, Parser::str('ab'))->map($count)->run('ababab'),
        new Good(3, 6),
        'Parser::times(n, p)->map(count) when successful must produce n.'
      );

      $this->assertEquals(
        Parser::regex('/^\d\d\w$/')->run('12a'),
        new Good('12a', 3),
        'Parser::regex(pattern)->run(s) when successful must produce s.'
      );
    }

}

