<?php

use api\routes\Route;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class FilmRoutes extends Route
{

    public static function register_routes(App $app)
    {
        $app->get('/programmazione', self::class . ':get_programmazione');
        $app->get('/programmazione/{titolo_film}', self::class . ':get_film');
    }

    public function get_programmazione(Request $request, Response $response)
    {
        $result = false;

        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT film.titolo, orario.orario1, orario.orario2, orario.orario3, sala.numero 
                                        FROM ((film INNER JOIN proiezione ON film.id = proiezione.id_film) 
                                        INNER JOIN sala ON sala.id = proiezione.id_sala) 
                                        INNER JOIN orario ON orario.id = proiezione.id_orario 
                                        WHERE UTC_DATE() BETWEEN proiezione.data_inizio AND proiezione.data_fine";

            $stmt = $con->prepare($query);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows()) {
                $stmt->bind_result($titolo, $orario1, $orario2, $orario3, $sala);

                $films = array();

                while ($stmt->fetch()) {
                    $temp = array();
                    $temp['titolo'] = $titolo;
                    $temp['orario1'] = $orario1;
                    $temp['orario2'] = $orario2;
                    $temp['orario3'] = $orario3;
                    $temp['sala'] = $sala;
                    array_push($films, $temp);
                }

                $result = true;
                $this->message = "ci sono films";
                $response = self::get_response($response, $result, 'films', $films);
            } else {
                $this->message = "non ci sono films";
                $response = self::get_response($response, $result, 'films', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'films', false);
        }

        return $response;
    }

    public function get_film(Request $request, Response $response)
    {
        $result = false;

        $titoloFilm = $request->getAttribute('titolo_film');

        $con = DBController::getConnection();

        if ($con) {
            $query = "SELECT titolo, nazione, anno, genere, durata, regia, cast, produzione, distribuzione, data_uscita, trama FROM film WHERE titolo = ?";

            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $titoloFilm);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows()) {
                $stmt->bind_result($titolo, $nazione, $anno, $genere, $durata, $regia, $cast, $produzione, $distribuzione, $data_uscita, $trama);
                $stmt->fetch();

                $film['titolo'] = $titolo;
                $film['nazione'] = $nazione;
                $film['anno'] = $anno;
                $film['genere'] = $genere;
                $film['durata'] = $durata;
                $film['regia'] = $regia;
                $film['cast'] = $cast;
                $film['produzione'] = $produzione;
                $film['distribuzione'] = $distribuzione;
                $film['data_uscita'] = $data_uscita;
                $film['trama'] = $trama;

                $result = true;
                $this->message = "il film c'è";
                $response = self::get_response($response, $result, 'film', $film);
            } else {
                $this->message = "il film non c'è";
                $response = self::get_response($response, $result, 'film', false);
            }
        } else {
            $this->message = "database non connesso";
            $response = self::get_response($response, $result, 'film', false);
        }

        return $response;
    }
}