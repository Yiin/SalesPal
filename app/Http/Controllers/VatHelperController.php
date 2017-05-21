<?php

namespace App\Http\Controllers;

use Exception;
use View;

use Send;

use App\Http\Response;

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
            $vNum = $result->vatNumber;
            $rDate = $result->requestDate;
            $fName = $result->name;
            $fAdrr = $result->address;

            //print value that has been passed to var
            //passed vars
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'results' => [
                'Country code' => $cCode,
                'Vat Number' => $vNum,
                'Search date' => $rDate,
                'Name' => $fName,
                'Adress' => $fAdrr
            ]
        ]);

    }
}