<?php

class User
{

    public static function get_utente_by_id($id)
    {
        $con = DBController::getConnection();

        $query = "SELECT id, username, email, token_fcm, notifica FROM utente WHERE id = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows() > 0) {
            $stmt->bind_result($id, $username, $email, $token, $notifica);

            $utente = array();

            while ($stmt->fetch()) {
                $temp = array();
                $temp['id'] = $id;
                $temp['username'] = $username;
                $temp['email'] = $email;
                $temp['token_fcm'] = $token;
                $temp['notifica'] = $notifica;
                array_push($utente, $temp);

                return $utente;
            }
        } else {
            return false;
        }
    }

    public static function get_utente_by_username($username)
    {
        $con = DBController::getConnection();

        $query = "SELECT id, username, email, token_fcm, notifica FROM utente WHERE username = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows() > 0) {
            $stmt->bind_result($id, $username, $email, $token, $notifica);

            $utente = array();

            while ($stmt->fetch()) {
                $temp = array();
                $temp['id'] = $id;
                $temp['username'] = $username;
                $temp['email'] = $email;
                $temp['token_fcm'] = $token;
                $temp['notifica'] = $notifica;
                array_push($utente, $temp);

                return $utente;
            }
        } else {
            return false;
        }
    }

    public static function get_utente_by_email($email)
    {
        $con = DBController::getConnection();

        $query = "SELECT id, username, email, token_fcm, notifica FROM utente WHERE email = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows() > 0) {
            $stmt->bind_result($id, $username, $email, $token, $notifica);

            $utente = array();

            while ($stmt->fetch()) {
                $temp = array();
                $temp['id'] = $id;
                $temp['username'] = $username;
                $temp['email'] = $email;
                $temp['token_fcm'] = $token;
                $temp['notifica'] = $notifica;
                array_push($utente, $temp);

                return $utente;
            }
        } else {
            return false;
        }
    }

    public static function get_utente_by_token($token)
    {
        $con = DBController::getConnection();

        $query = "SELECT id, username, email, token_fcm, notifica FROM utente WHERE token_fcm = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows() > 0) {
            $stmt->bind_result($id, $username, $email, $token, $notifica);

            $utente = array();

            while ($stmt->fetch()) {
                $temp = array();
                $temp['id'] = $id;
                $temp['username'] = $username;
                $temp['email'] = $email;
                $temp['token_fcm'] = $token;
                $temp['notifica'] = $notifica;
                array_push($utente, $temp);

                return $utente;
            }
        } else {
            return false;
        }
    }

    public static function get_tokens()
    {
        $con = DBController::getConnection();

        $query = "SELECT token_fcm, notifica FROM utente";

        $stmt = $con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($token, $notifica);

        $tokens = array();

        while ($stmt->fetch()) {
            if($notifica == 'T'){
                $tokens[] = $token;
            }
        }

        return $tokens;

    }

}