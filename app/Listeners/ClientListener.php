<?php

namespace App\Listeners;

use App\Events\ClientVatWasChecked;
use App\Models\Client;
use Carbon;

/**
 * Class ClientListener.
 */
class ClientListener
{
    /**
     * @param ClientVatWasChecked $event
     */
    public function checkedVat(ClientVatWasChecked $event)
    {
        $client = $event->client;

        // if the payment was from a credit we need to refund the credit
        if (! $client) {
            return;
        }

        $data = [
            'is_valid' => $event->success && $event->data->name !== "---",
            'country_code' => $event->data->country_code ?? '',
            'vat_number' => $event->data->vat_number ?? '',
            'address' => $event->data->address ?? null,
            'name' => $event->data->name ?? null
        ];
        $client->vatChecks()->create($data);
    }
}
