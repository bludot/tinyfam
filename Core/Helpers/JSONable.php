<?php

namespace Core\Helpers;

interface JSONable {

    // Transforms an object to JSON given certain parameters
    public function toJSON($params);

    // Reconstructs the data of this structure from JSON
    public function fromJSON($data);

}