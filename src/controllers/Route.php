<?php

namespace api\routes;

use Slim\App;
use Slim\Http\Response;

abstract class Route
{
    public $message;

    public static abstract function register_routes(App $app);

    public function get_response(Response $response, $result, $data_key, $data = false, array $response_params = [])
    {
        $json_data = array(
            'result' => $result,
            'message' => $this->message,
            $data_key => $data
        );
        if (!empty($response_params)) {
            $response = $response->withStatus($response_params['status'])
                ->withHeader('Content-Type', $response_params['content-type']);
        } else {
            $response = $response->withStatus(200);
        }
        return $response->withJson($json_data);
    }

}