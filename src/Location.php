<?php

class Location {
  private $input;
  private $offset = 0;
  function __construct($i, $o = 0) {
    $this->input = $i;
    $this->offset = $o;
  }
  function toError($msg) {
    return new ParserError(array(array($this, $msg)));
  }
  function advanceBy($n) {
    return new Location($this->input, $this->offset + $n);
  }
  function input() {
    return $this->input;
  }
  function offset() {
    return $this->offset;
  }
}


