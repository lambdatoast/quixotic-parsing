<?php

trait ParsingPrimitives {

  // Functor instance

  function map(callable $f) { 
    return $this->chain(function ($a) use ($f) {
      return $this::succeed($f($a));
    });
  }

}
