<?php

abstract class CommitStatus {
  abstract function fold(callable $uncommitted, callable $committed);
}
