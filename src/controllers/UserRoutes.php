<?php

use api\routes\Route;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once 'User.php';

class UserRoutes extends Route
{

    public static function register_routes(App $app)
    {
        $app->post('/utente/registrazione', self::class . ':registrazione_utente');
    }

    public function registrazione_utente(Request $request, Response $response)
    {
        $result = false;

        $requestData = $request->getParsedBody();

        $username = $requestData['username'];
        $email = $requestData['email'];
        $password = $requestData['password'];
        $token = $requestData['token'];

        if ($username && $email && $password && $token) {
            if (User::dispositivo_esistente($token)) {
                $this->message = "dispositivo già registrato";
            } else {
                if (User::username_esistente($username) || User::email_esistente($email)) {
                    $this->message = "utente esistente";
                } else {
                    $con = DBController::getConnection();

                    $query = "INSERT INTO utente (username, email, password, token_fcm) VALUES (?, ?, ?, ?)";

                    $stmt = $con->prepare($query);
                    $stmt->bind_param("ssss", $username, $email, md5($password), $token);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt) {
                        $result = true;
                        $this->message = "registrazione effettuata";
                        $response = self::get_response($response, $result, 'registrazione', true);
                    }
                }
            }
        } else {
            $this->messages = "parametri mancanti";
        }
        if (!$stmt) {
            $response = self::get_response($response, $result, 'registrazione', false);
        }
        return $response;
    }


}