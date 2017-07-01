<?php

namespace App\Http\Controllers;

use Exception;
use View;

use Send;

use App\Http\Response;

use App\Events\ClientVatWasChecked;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

use SoapClient;

$cCode = "";
$vNum = "";

class VatHelperController extends Controller
{

    public function VatRequest(Request $request)
    {

        $vat = $request->get('vat');
        $register_check = !!$request->get('save');

        $vat_CC = mb_substr($vat, 0, 2);

        $vat_VN = mb_substr($vat, 2, 20);


        try {
            //define method for sending information
            $opts = array(
                'http' => array(
                    'user_agent' => 'PHPSoapClient'
                )
            );
            $context = stream_context_create($opts);

            $client = new SoapClient(
                'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
                array('stream_context' => $context,
                    'cache_wsdl' => WSDL_CACHE_NONE)
            );

            $result = $client->checkVat(
                array(
                    'countryCode' => $vat_CC,
                    'vatNumber' => $vat_VN
                )
            );

            //pass value to variable
            $cCode = $result->countryCode;
            $vNum =  $result->vatNumber;
            $rDate = $result->requestDate;
            $fName = $result->name;
            $fAdrr = $result->address;

            //print value that has been passed to var
            //passed vars
        } catch (Exception $e) {
            $data = [
                'country_code' => '',
                'vat_number' => $vat,
                'message' => $e->getMessage(),
            ];

            if ($register_check) {
                event(new ClientVatWasChecked(false, (object) $data));
            }

            return response()->json($data, 500);
        }

        $data = [
            'country_code' => $result->countryCode,
            'vat_number' => $result->vatNumber,
            'search_date' => $result->requestDate,
            'name' => $result->name,
            'address' => $result->address,
        ];

        if ($register_check) {
            event(new ClientVatWasChecked(true, (object) $data));
        }

        return response()->json($data);

    }
}