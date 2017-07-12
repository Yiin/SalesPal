<?php

namespace App\Events;

use App\Models\Client;
use App\Models\VatCheck;

class ClientVatWasChecked extends Event
{
    /**
     * @var Client
     */
    public $client;

    /**
     * @var boolean
     */
    public $success;

    /**
     * @var object
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param boolean $success
     * @param object $data
     */
    public function __construct($success, $data)
    {
        $this->client = Client::withTrashed()->where('public_id', $data->client_id)->first();
        $this->success = $success;
        $this->data = $data; 
    }
}
