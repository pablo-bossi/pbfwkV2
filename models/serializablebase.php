<?php

namespace Models;

abstract class SerializableBase extends \Models\Base implements \JsonSerializable {
  public function jsonSerialize() {
    $json = new \stdClass();
    foreach ($this as $key => $value) {
      $json->$key = $value;
    }
    return $json;
  }
}

?>