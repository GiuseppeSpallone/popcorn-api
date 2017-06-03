<?php

use api\routes\Route;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class InfoRoutes extends Route
{
    public static function register_routes(App $app)
    {
        $app->get('/info/prezzi', self::class . ':get_prezzi');
        $app->get('/info/sale', self::class . ':get_sale');
    }

    public function get_prezzi(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT tipo, prezzo FROM biglietto";

            $stmt = $con->prepare($query);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows()) {
                $stmt->bind_result($tipo, $prezzo);

                $prezzi = array();

                while ($stmt->fetch()) {
                    $temp = array();
                    $temp['tipo'] = $tipo;
                    $temp['prezzo'] = $prezzo;
                    array_push($prezzi, $temp);
                }

                $result = true;
                $this->message = "ci sono prezzi";
                $response = self::get_response($response, $result, 'prezzi', $prezzi);
            } else {
                $this->message = "non ci sono prezzi";
                $response = self::get_response($response, $result, 'prezzi', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'prezzi', false);
        }

        return $response;
    }

    public function get_sale(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT numero, posti FROM sala";

            $stmt = $con->prepare($query);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows()) {
                $stmt->bind_result($numero, $posti);

                $sale = array();

                while ($stmt->fetch()) {
                    $temp = array();
                    $temp['numero'] = $numero;
                    $temp['posti'] = $posti;
                    array_push($sale, $temp);
                }

                $result = true;
                $this->message = "ci sono sale";
                $response = self::get_response($response, $result, 'sale', $sale);
            } else {
                $this->message = "non ci sono sale";
                $response = self::get_response($response, $result, 'sale', false);
            }
        }else{
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'sale', false);
        }

        return $response;
    }

}