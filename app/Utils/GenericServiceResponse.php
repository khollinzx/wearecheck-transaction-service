<?php

namespace App\Utils;

class GenericServiceResponse
{

    /**
     * @param bool $status
     * @param string $message
     * @param mixed $data
     */
    public function __construct(
        public bool $status = false,
        public string $message = ServiceResponseMessage::ERROR_OCCURRED,
        public mixed $data = []
    ) { }

}
