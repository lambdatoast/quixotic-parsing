<?php

class LocationTest extends \PHPUnit_Framework_TestCase {
    public function testEqual() {
      $l1 = new Location('abcd', 42);
      $l2 = new Location('abcd', 42);
      $l3 = new Location('abcd', 41);
      $l4 = new Location('abc', 42);

      $this->assertTrue(
        $l1->equal($l2)
      );

      $this->assertFalse(
        $l1->equal($l3)
      );

      $this->assertFalse(
        $l1->equal($l4)
      );

    }

    public function testManipulation() {
      $l1 = new Location('abcd', 42);

      $this->assertEquals(
        $l1->advanceBy(1)->offset(),
        43
      );
    }
}


