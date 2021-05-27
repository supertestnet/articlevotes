<?php

namespace App\Http\Controllers;

use App\Handlers\ResponseData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LightningController
{
    // delete LNBits wallet
    // take wallet info as param, return nothing
    public function deleteLNBitsWallet( $lnbits_url, $wallet_id, $lnbits_apikey )
    {
        ob_start();
        $url = $lnbits_url . '/usermanager/api/v1/wallets/' . $wallet_id;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $lnbits_apikey 
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $head = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
        $data = ob_get_clean();
        return $data;
    }

    // generate a LNBits wallet
    // take lnbits url, lnbits api key, user id, wallet name, and admin id as params, return wallet info. Note that before this function can work, your lnbits instance must have the user manager extension installed and one user created. Once that is done, your user_id can be found in the Users section of the User Manager extension page and your admin_id can be found in the url of the User Manager extension page after the parameter "usr=". Also note that one user can have many wallets so you can have a user called something like "Lightning Video Game"Ã‚Â and then each player can have a unique wallet belonging to that one user.
    public function generateLNBitsWallet( $lnbits_url, $user_id, $wallet_name, $admin_id, $lnbits_apikey )
    {
        ob_start();
        $payload = '{"user_id": "' . $user_id . '", "wallet_name": "' . $wallet_name . '", "admin_id": "' . $admin_id . '"}';
        $url = $lnbits_url . '/usermanager/api/v1/wallets';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $lnbits_apikey,
                'Content-Type: application/json'
        ));
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        $head = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
        $data = ob_get_clean();
        return $data;
    }

    // pay a Lightning invoice using an LNBits wallet
    // take lnbits url, invoice, and lnbits api key as params, return success or error or failure
    public function payInvoice( $lnbits_url, $invoice, $lnbits_apikey )
    {
        ob_start();
        $payload = '{"out": true, "bolt11": "' . $invoice . '"}';
        $url = $lnbits_url . '/api/v1/payments';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $lnbits_apikey,
                'Content-Type: application/json'
        ));
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        $head = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
        $data = ob_get_clean();
	$data = json_decode( $data, true );
        if ( $data["payment_hash"] ) {
                return 1;
        }
	if ( !$data["payment_hash"] && $data["message"] ) {
		$error["error"] = $data["message"];
		return json_encode( $error );
	} else {
        $error["error"] = json_encode( $data );
        return json_encode( $error );
    }
        return 0;
    }

    // request a Lightning invoice from a LNBits wallet
    // take lnbits url, amount, memo, and lnbits api key as params, return generated invoice and payment hash
//    public function requestInvoice( $lnbits_url, $amount, $memo, $lnbits_apikey, $webhook )
    public function requestInvoice( $lnbits_url, $amount, $memo, $lnbits_apikey )
    {
        ob_start();
//        $payload = '{"out": false, "amount": ' . $amount . ', "memo": "' . $memo . '", "webhook": "' . $webhook . '"}';
//        $payload = '{"num_satoshis":' . $amount . ', "memo":' . $memo . '}';
	$payload = '{"num_satoshis":' . $amount . ',"memo":"' . $memo . '"}';
        $url = $lnbits_url . '/api/v1/payments';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $lnbits_apikey,
                'Content-Type: application/json'
        ));
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        $head = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
        $data = ob_get_clean();
        $data = json_decode( $data, true );
        $result["invoice"] = $data["payment_request"];
        $result["pmthash"] = $data["payment_hash"];
        $result["pmthash"] = $data["id"];
        $json = json_encode( $result );
        return $json;
    }

    // check a Lightning invoice from a LNBits wallet
    // take lnbits url, payment hash, and lnbits api key as params, return 1 if paid or 0 otherwise
    public function checkInvoice( $lnbits_url, $pmthash, $lnbits_apikey )
    {
        ob_start();
        $url = $lnbits_url . '/api/v1/payments';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $lnbits_apikey,
                'Content-Type: application/json'
        ));
        $head = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
        $paychecker = ob_get_clean();
        $data = ob_get_clean();
        if ( $paychecker == 1 ) {$paychecker = "true";}
        if ( strpos( $paychecker, "true" ) !== false ) {
            $paychecker = 1;
        } else {
            $paychecker = 0;
        }
        return $paychecker;
    }
}

?>
