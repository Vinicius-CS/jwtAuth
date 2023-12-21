<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Response;

class ResponseComponent extends Component
{
    public function json($data = [], $statusCode = 200)
    {
        $response = new Response();
        $response = $response->withType('application/json');
        $response = $response->withStatus($statusCode);
        $response = $response->withStringBody(json_encode($data));

        return $response;
    }

    public function write($data = '', $statusCode = 200)
    {
        $response = new Response();
        $response = $response->withStatus($statusCode);
        $response->getBody()->write($data);

        return $response;
    }
}