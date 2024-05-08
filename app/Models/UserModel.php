<?php

namespace app\Models;

use app\Models\DatabaseModel;
use \PDO;


class UserModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function getUserInfo( string $username, string $password ) : ?array {

        // Search user from the database users
        $query = $this->pdo->prepare("SELECT users.password, users.userId, users.isAdmin FROM users WHERE users.username = ?");
        $query->execute([$username]);
        [$userPassword, $userId, $isAdmin] = $query->fetch();

        // Return null if user password is empty
        if (empty($userPassword)) {
            return null;
        }

        // Return user ID if user password is found and it matches the one in the database
        if ($userPassword == $password) {
            $userInfo = [
                "userId"   => $userId,
                "isAdmin"  => $isAdmin
            ];
            return $userInfo;
        }

        return null;
    }
}
