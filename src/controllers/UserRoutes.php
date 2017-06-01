<?php
/**
 * Created by PhpStorm.
 * User: peppe
 * Date: 31/05/2017
 * Time: 17:18
 */

use api\routes\Route;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class UserRoutes extends Route
{

    public static function register_routes(App $app)
    {
        $app->post('/utente/registrazione', self::class . ':registrazione_utente');
        $app->post('/utente/accesso', self::class . ':accesso_utente');
        $app->get('/utente/token', self::class . ':get_dispositivi'); //per testare
        $app->get('/notification', self::class . ':notification_all');
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
            if (self::_dispositivo_esistente($token)) {
                $this->message = "dispositivo già registrato";
            } else {
                if (self::_utente_esistente($username, $email)) {
                    $this->message = "utente esistente";
                } else {
                    $con = DBController::getConnection();

                    $query = "INSERT INTO utente (username, email, password, token) VALUES (?, ?, ?, ?)";

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

    public function get_dispositivi(Request $request, Response $response)
    {
        $result = false;

        if ($dispositivi = self::_get_dispositivi()) {
            $result = true;
            $this->message = "ci sono dispositivi registrati";
            $response = self::get_response($response, $result, 'dispositivi', $dispositivi);
        } else {
            $this->message = "error";
            $response = self::get_response($response, $result, 'dispositivi', false);
        }

        return $response;
    }

    public function notification_all(Request $request, Response $response)
    {
        $message = array("message" => "ci sono riuscito cazzo");

        $url = "https://fcm.googleapis.com/fcm/send";

        $fields = array(
            'registration_ids' => self::_get_dispositivi(),
            'data' => $message
        );

        $headers = array(
            'Authorization:key = 
                    AAAARASmfMY:APA91bEVABajL8Ia8dfegCVCx_IHJYicXuYZuCyik0Hmr3-u1ym4-LmmBaKqKXX_zjeoWQaw9F8HHSlB3yoKlxpPh765Wh-VcArrGO6W62kmg61P_Tz3hSsDJujNjJ-UERBJq5jwV2QT',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        return $result;
    }

    private function _utente_esistente($username, $email)
    {
        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT * FROM utente WHERE username = ? OR email = ?";

            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            $num_rows = $stmt->num_rows();

            if ($num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function _dispositivo_esistente($token)
    {
        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT * FROM utente WHERE token = ?";

            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->store_result();

            $num_rows = $stmt->num_rows();

            if ($num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function _get_dispositivi()
    {
        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT token FROM utente";

            $stmt = $con->prepare($query);
            $stmt->execute();
            $stmt->bind_result($dispositivo);

            $dispositivi = array();

            while ($stmt->fetch()) {
                $dispositivi[] = $dispositivo;
            }

            return $dispositivi;

        }

    }
}