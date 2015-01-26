<?php

class ResultTest extends \PHPUnit_Framework_TestCase {

    public function testGoodEqual() {

      $a = new Good('abcd', 42);
      $b = new Good('abcd', 42);
      $c = new Good('abcd', 41);
      $d = new Good('abc', 42);

      $this->assertTrue(
        $a->equal($b)
      );

      $this->assertFalse(
        $a->equal($c)
      );

      $this->assertFalse(
        $a->equal($d)
      );

    }

    public function testCharsConsumed() {
      $x = new Good('abcd', 42);
      $f = function ($a) {
        return new Good('efgh', 17);
      };

      $this->assertTrue(
        $x->chain($f)->getCharsConsumed()->equal(new Some(17))
      );
    }

}
