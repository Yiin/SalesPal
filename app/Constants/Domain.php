<?php

namespace App\Constants;

class Domain
{
    const SALESPAL_COM = 1;
    const SALESPAL_SERVICES = 2;

    public static function getDomainFromId($id)
    {
        switch ($id) {
            case static::SALESPAL_COM:
                return 'salespal.ie';
            case static::INVOICE_SERVICES:
                return 'invoice.services';
        }

        return 'salespal.com';
    }

    public static function getLinkFromId($id)
    {
        return 'https://app.' . static::getDomainFromId($id);
    }

    public static function getEmailFromId($id)
    {
        return 'maildelivery@' . static::getDomainFromId($id);
    }
}
