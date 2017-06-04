<?php

class User
{
    public static function username_esistente($username)
    {
        $con = DBController::getConnection();

        $query = "SELECT * FROM utente WHERE username = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        $num_rows = $stmt->num_rows();

        if ($num_rows > 0) {
            return $num_rows;
        } else {
            return false;
        }
    }

    public static function email_esistente($email)
    {
        $con = DBController::getConnection();

        $query = "SELECT * FROM utente WHERE email = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        $num_rows = $stmt->num_rows();

        if ($num_rows > 0) {
            return $num_rows;
        } else {
            return false;
        }

    }

    public static function dispositivo_esistente($token)
    {
        $con = DBController::getConnection();

        $query = "SELECT * FROM utente WHERE token_fcm = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        $num_rows = $stmt->num_rows();

        if ($num_rows > 0) {
            return $num_rows;
        } else {
            return false;
        }
    }

    public static function get_tokens()
    {
        $con = DBController::getConnection();

        $query = "SELECT token_fcm FROM utente";

        $stmt = $con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($token);

        $tokens = array();

        while ($stmt->fetch()) {
            $tokens[] = $token;
        }

        return $tokens;

    }

    public static function get_token_by_username($username)
    {
        $con = DBController::getConnection();

        $query = "SELECT token_fcm FROM utente WHERE username = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($token);

        $tokens = array();

        while ($stmt->fetch()) {
            $tokens[] = $token;
        }

        return $tokens;
    }

}