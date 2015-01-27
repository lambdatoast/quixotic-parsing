<?php

/**
 * All functions in this traits are 
 * implementations of functions described 
 * in the Parsing interface, and are 
 * strictly derived from the (conceptually) abstract 
 * Parsing functions.
 *
 */
trait ParsingPrimitives {

  // Functor instance

  function map(callable $f) { 
    return $this->chain(function ($a) use ($f) {
      return $this::succeed($f($a));
    });
  }

  static function times($n, Parsing $p) {
    return $n > 0 ? $p->map2($p, self::times($n - 1, $p), function ($a, $as) { 
                                return array_merge(array($a), $as); 
                              })
                  : self::succeed(array());
  }

  static function map2(Parsing $a, Parsing $b, callable $f) {
    return $a->chain(function ($x) use ($f, $b) {
      return $b->map(function ($y) use ($f, $x) {
        return $f($x, $y);
      });
    });
  }

  static function product(Parsing $p1, Parsing $p2) {
    return $p1->chain(function ($a) use ($p2) {
      return $p2->map(function ($b) use ($a) {
        return array($a, $b);
      });
    });
  }

  static function lazy_product(Parsing $p1, callable $p2f) {
    return $p1->chain(function ($a) use ($p2f) {
      return $p2f()->map(function ($b) use ($a) {
        return array($a, $b);
      });
    });
  }


}
