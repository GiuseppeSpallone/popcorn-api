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
        $app->post('/utente/accesso', self::class . ':accesso_utente');
        $app->get('/utente/{username}', self::class . ':get_utente_by_username');
        $app->delete('/utente/{username}/delete', self::class . ':delete_utente_by_username');
        $app->put('/utente/{username}/edit_notifica', self::class . ':edit_notifica');
    }

    public function registrazione_utente(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $requestData = $request->getParsedBody();

            $username = $requestData['username'];
            $email = $requestData['email'];
            $password = $requestData['password'];
            $token = $requestData['token'];

            if ($username && $email && $password && $token) {
                if (User::get_utente_by_token($token)) {
                    $this->message = "dispositivo già registrato";
                    $response = self::get_response($response, $result, 'registrazione', false);
                } else {
                    if (User::get_utente_by_username($username) || User::get_utente_by_email($email)) {
                        $this->message = "utente esistente";
                        $response = self::get_response($response, $result, 'registrazione', false);
                    } else {

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
                $this->message = "parametri mancanti";
                $response = self::get_response($response, $result, 'registrazione', false);
            }

        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'registrazione', false);
        }

        return $response;
    }

    public function accesso_utente(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $requestData = $request->getParsedBody();

            $username = $requestData['username'];
            $password = $requestData['password'];

            if ($username && $password) {

                $query = "SELECT id FROM utente WHERE username = ? AND password = ?";

                $stmt = $con->prepare($query);
                $stmt->bind_param("ss", $username, md5($password));
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows) {
                    $result = true;
                    $this->message = "accesso effettuato";
                    $response = self::get_response($response, $result, 'accesso', true);
                } else {
                    $this->message = "username o password non validi";
                    $response = self::get_response($response, $result, 'accesso', true);
                }
            } else {
                $this->message = "parametri mancanti";
                $response = self::get_response($response, $result, 'registrazione', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'registrazione', false);
        }

        return $response;
    }

    public function get_utente_by_username(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {

            $username = $request->getAttribute('username');

            $utente = User::get_utente_by_username($username);

            if ($utente) {
                $result = true;
                $this->message = "utente esistente";
                $response = self::get_response($response, $result, 'utente', $utente);
            } else {
                $this->message = "utente non esistente";
                $response = self::get_response($response, $result, 'utente', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'utente', false);
        }

        return $response;
    }

    public function delete_utente_by_username(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {

            $username = $request->getAttribute('username');

            $utente = User::get_utente_by_username($username);

            if ($utente) {
                $query = "DELETE FROM utente WHERE username = ?";

                $stmt = $con->prepare($query);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt) {
                    $result = true;
                    $this->message = "utente cancellato";
                    $response = self::get_response($response, $result, 'delete', true);
                } else {
                    $this->message = "utente non cancellato";
                    $response = self::get_response($response, $result, 'delete', true);
                }
            } else {
                $this->message = "utente non esistente";
                $response = self::get_response($response, $result, 'delete', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'delete', false);
        }

        return $response;
    }

    public function edit_notifica(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {

            $username = $request->getAttribute('username');

            $utente = User::get_utente_by_username($username);

            if ($utente) {
                $notifica = $request->getHeader('notifica');

                if ($notifica[0] == 'T' || $notifica[0] == 'F') {
                    $query = "UPDATE utente SET notifica = ? WHERE username = ?";

                    $stmt = $con->prepare($query);
                    $stmt->bind_param("ss", $notifica[0], $username);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt) {
                        $result = true;
                        $this->message = "permesso notifica modificato";
                        $response = self::get_response($response, $result, 'edit', true);
                    } else {
                        $this->message = "permesso notifica non modificato";
                        $response = self::get_response($response, $result, 'edit', true);
                    }
                } else {
                    $this->message = "inserisci T se vuoi abilitare le notifiche, oppure inserisci F se vuoi disasttivare le notifiche";
                    $response = self::get_response($response, $result, 'edit', true);
                }
            } else {
                $this->message = "utente non esistente";
                $response = self::get_response($response, $result, 'edit', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'edit', false);
        }

        return $response;
    }

}