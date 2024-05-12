<?php

class User
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function generateAccessToken($userId)
    {
        return md5($userId . time());
    }

    public function checkAcessToken($accessToken)
    {
        $query = "SELECT id, administrator FROM users WHERE access_token = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $accessToken);
        $stmt->execute();
        $stmt->bind_result($id, $administrator);
        $stmt->fetch();
        $stmt->close();
        if ($id !== null && $administrator !== null) {
            return array('id' => $id, 'role' => $administrator ? 'admin' : 'user');
        } else {
            return false;
        }
    }

    public function getAllUsers($data)
    {
        /* GET ALL USERS + QUESTIONS + ANSWERS
        $query = "SELECT users.username, users.password, users.administrator, questions.date, 
        questions.user_id , questions.subject , questions.question , questions.type_id, questions.code, 
        questions.qr_code, answers.question_id, answers.answer, answers.correct, answers.count, types.type
        FROM users 
        LEFT JOIN questions ON users.id = questions.user_id  
        LEFT JOIN answers ON answers.question_id = questions.id
        LEFT JOIN types ON answers.type_id = types.id";
        */
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        if (!$accessTokenData) {
            return false;
        }
        if ($accessTokenData['role'] !== "admin") {
            return false;
        }

        $query = "SELECT id, username, administrator FROM users";
        $result = mysqli_query($this->conn, $query);
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        return $users;
    }

    public function login($data)
    {
        $username = $data['username'];
        $password = $data['password'];
        $query = "SELECT id, username, password, administrator FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        if ($user && password_verify($password, $user['password'])) {
            $accessToken = $this->generateAccessToken($user['id']);
            $stmt = $this->conn->prepare("UPDATE users SET access_token = ? WHERE username = ?");
            $stmt->bind_param("ss", $accessToken, $username);
            $stmt->execute();
            $stmt->close();
            $user['access_token'] = $accessToken;
            unset($user['password']);
            return $user;
        } else {
            return null;
        }
    }
    public function logout($data)
    {
        $accessToken = $data['access_token'];
        $query = "UPDATE users SET access_token = NULL WHERE access_token = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $accessToken);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }

    }

    public function checkUsername($data)
    {
        $username = $data['username'];
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function createUser($data)
    {
        $username = $data['username'];
        $password = $data['password'];
        $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return false;
        }
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, administrator) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return true;
    }

    public function updateUser($id, $data)
    {
        $accessToken = $data['access_token'];
        $username = $data['username'];
        $password = $data['password'];

        $accessTokenData = $this->checkAcessToken($accessToken);
        if (!$accessTokenData || $this->checkUsername($data)) {
            return false;
        }
        $role = $accessTokenData['role'];
        if (($role === "admin") || (($role === "user") && ($id == $accessTokenData['id']))) {
            $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
            $stmt = $this->conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $hashed_password, $id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteUser($id, $data)
    {
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        if (!$accessTokenData) {
            return false;
        }
        $role = $accessTokenData['role'];
        if (($role === "admin") || ($role === "user" && $id == $accessTokenData['id'])) {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $affectedRows > 0;
        } else {
            return false;
        }
    }
}