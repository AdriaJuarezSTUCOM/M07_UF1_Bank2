<?php namespace ComBank\Support\Traits;

require 'c:\xampp\htdocs\M07-BackEnd\M07_UF1_Bank2\vendor\autoload.php';

use ComBank\Transactions\Contracts\BankTransactionInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

trait APITrait {
    public function convertBalance($balance, $originalCurrency = "EUR", $convertedCurrency = "USD") : float {
        $headers = array(
            'Accept' => 'application/json',
            'x-api-key' => 'sk_c1e7d228872048f8892955338a2e6eb1',
        );

        $client = new Client();

        // Define array of request body.
        $request_body = array(
            "amount" => $balance,
            "from" => $originalCurrency,
            "to" => $convertedCurrency
        );

        try {
            $response = $client->request('GET', 'https://api.manyapis.com/v1-convert-currency', array(
                'headers' => $headers,
                'query' => $request_body,
            ));
            
            // Decodificar el cuerpo de la respuesta y obtener el valor 'convertedAmount'
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['convertedAmount'] ?? 0; // Retorna el valor o 0 si no existe
        } catch (BadResponseException $e) {
            // Maneja excepciones o errores de la API.
            print_r($e->getMessage());
           return 0; // Retorna 0 en caso de error
        }
    }

    public function validateEmail($email): bool{
        $headers = array(
            'Accept' => 'application/json',
            'x-api-key' => 'sk_c1e7d228872048f8892955338a2e6eb1',
        );

        $client = new Client();

        // Define array of request body.
        $request_body = array(
            "email" => $email
        );

        try {
            $response = $client->request('GET','https://api.manyapis.com/v1-get-email', array(
                'headers' => $headers,
                'query' => $request_body,
            )
            );
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['validFormat'] && !$data['isDisposable'] ?? 0; // Retorna el valor o 0 si no existe
        }
        catch (BadResponseException $e) {
            // handle exception or api errors.
            print_r($e->getMessage());
            return 0;
        }
    }

    public function detectFraud(BankTransactionInterface $bankTransaction = null): bool {
        $headers = array(
            'Accept' => 'application/json',
        );

        $client = new Client();

        try {
            $response = $client->request('GET','https://6734f9135995834c8a918de8.mockapi.io/movement', array(
                'headers' => $headers,
            )
            );
            $data = json_decode($response->getBody()->getContents(), true);
            
            foreach($data as $key => $value){
                if( $bankTransaction->getTransactionInfo() == $value["type"]){
                    if($bankTransaction->getAmount()>$value["maxAmount"]){
                        return !$value["fraud"];
                    }
                }    
            }

            return true;
        }
        catch (BadResponseException $e) {
            // handle exception or api errors.
            print_r($e->getMessage());
            return 0;
        }
    }

    public function validateLocation($ip=null): bool{
        $headers = array(
            'Accept' => 'application/json',
            'x-api-key' => 'sk_c1e7d228872048f8892955338a2e6eb1',
        );

        $client = new Client();

        // Define array of request body.
        $request_body = array(
            "ip" => $ip
        );

        try {
            $response = $client->request('GET','https://api.manyapis.com/v1-get-ipv4-detail', array(
                'headers' => $headers,
                'query' => $request_body,
            )
            );
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['city']['name'] == "Barcelona"; // Retorna el valor o 0 si no existe
        }
        catch (BadResponseException $e) {
            // handle exception or api errors.
            print_r($e->getMessage());
            return 0;
        }
    }
}
