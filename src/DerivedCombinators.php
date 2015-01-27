<?php

/**
 * All functions in this traits are 
 * implementations of functions described 
 * in the Combinator interface, and are 
 * strictly derived from the (conceptually) abstract 
 * Combinator functions.
 *
 */
trait DerivedCombinators {

  // Functor instance

  function map(callable $f) { 
    return $this->chain(function ($a) use ($f) {
      return $this::succeed($f($a));
    });
  }

  static function times($n, Combinator $p) {
    return $n > 0 ? $p->map2($p, self::times($n - 1, $p), function ($a, $as) { 
                                return array_merge(array($a), $as); 
                              })
                  : self::succeed(array());
  }

  static function map2(Combinator $a, Combinator $b, callable $f) {
    return $a->chain(function ($x) use ($f, $b) {
      return $b->map(function ($y) use ($f, $x) {
        return $f($x, $y);
      });
    });
  }

  static function product(Combinator $p1, Combinator $p2) {
    return $p1->chain(function ($a) use ($p2) {
      return $p2->map(function ($b) use ($a) {
        return array($a, $b);
      });
    });
  }

  static function lazy_product(Combinator $p1, callable $p2f) {
    return $p1->chain(function ($a) use ($p2f) {
      return $p2f()->map(function ($b) use ($a) {
        return array($a, $b);
      });
    });
  }

  static function skipL(Combinator $p1, Combinator $p2) {
    return self::map2($p1, $p2, function ($_, $b) { return $b; });
  }

  static function skipR(Combinator $p1, Combinator $p2) {
    return self::map2($p1, $p2, function ($a, $_) { return $a; });
  }

}
