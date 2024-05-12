<?php

class Question
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function checkAcessToken($accessToken)
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
    private function isUserAuthorized($accessToken, $questionId)
    {
        $userIdFromToken = $this->checkAcessToken($accessToken)['id'];
        if (!$userIdFromToken) {
            return false;
        }
        $query = "SELECT user_id FROM questions WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $stmt->bind_result($userIdFromQuestion);
        $stmt->fetch();
        $stmt->close();
        return $userIdFromToken === $userIdFromQuestion;
    }
    private function isUserAuthorizedForAnswer($accessToken, $answerId)
    {
        $userIdFromToken = $this->checkAcessToken($accessToken)['id'];
        if (!$userIdFromToken) {
            return false;
        }
        $query = "SELECT question_id FROM answers WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $answerId);
        $stmt->execute();
        $stmt->bind_result($questionId);
        $stmt->fetch();
        $stmt->close();
        return $this->isUserAuthorized($accessToken, $questionId);
    }

    public function getAllQuestionAnswers($question_id, $data)
    {
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $this->isUserAuthorized($accessToken, $question_id))) {
            $query = "SELECT id, answer, count, correct FROM answers WHERE question_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = mysqli_fetch_assoc($result)) {
                $answers[] = $row;
            }
            $stmt->close();
            return $answers;
        } else {
            return false;
        }
    }

    public function getQuestionsByUserId($user_id, $data)
    {
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $user_id == $accessTokenData['id'])) {
            $query = "SELECT questions.date, 
        questions.user_id , questions.subject , questions.question , questions.type_id, questions.code, 
        questions.id as question_id, answers.answer, answers.correct, answers.count, types.type
        FROM questions 
        LEFT JOIN answers ON answers.question_id = questions.id
        LEFT JOIN types ON answers.type_id = types.id
        WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $questions = [];
            $result = $stmt->get_result();
            while ($row = mysqli_fetch_assoc($result)) {
                $questions[] = $row;
            }
            $stmt->close();
            return $questions;
        } else {
            return false;
        }
    }


    public function getQuestionByCode($code)
    {
        $query = "SELECT * FROM questions WHERE code = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $question = $result->fetch_assoc();
        $stmt->close();
        return $question;
    }
    public function getQuestionById($question_id, $data)
    {
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $this->isUserAuthorized($accessToken, $question_id))) {
            $query = "SELECT * FROM questions WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $question = $result->fetch_assoc();
            $stmt->close();
            return $question;
        } else {
            return false;
        }
    }

    public function createQuestion($data)
    {
        $question = $data["question"];
        $subject = $data["subject"];
        $type = $data["type"];
        if ($type != "open_ended" && $type != "multiple_choice") {
            return false;
        }
        $user_id = $data["user_id"];
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        $id = $accessTokenData['id'];

        if (($role === 'admin') || ($role === 'user' && $user_id == $id)) {
            do {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 5; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }

                $code = $randomString;
                $query = "SELECT COUNT(*) as count FROM questions WHERE code = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("s", $code);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
            } while ($row['count'] > 0);

            $query = "SELECT id FROM types WHERE type = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $result = $stmt->get_result();
            $type_id = $result->fetch_assoc();
            $type_id = $type_id['id'];
            $stmt->close();
            if (!$type_id) {
                return false;
            }
            $stmt = $this->conn->prepare("INSERT INTO questions (question, subject, type_id, user_id, code) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $question, $subject, $type_id, $user_id, $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return true;
        } else {
            false;
        }
    }
    public function createAnswer($data)
    {
        $answer = $data["answer"];
        $question_id = $data["question_id"];
        $correct = $data["correct"];

        $query = "SELECT type_id FROM questions WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $type_id = $result->fetch_assoc();
        $type_id = $type_id['type_id'];
        $stmt->close();
        if (!$type_id) {
            return false;
        }
        $query = "SELECT id FROM answers WHERE answer = ? AND question_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $answer, $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $answer_id = $result->fetch_assoc();
        $stmt->close();
        if ($answer_id) {
            $answer_id = $answer_id['id'];
            $query = "SELECT `count` FROM answers WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $answer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc();
            $count = $count['count'];
            $count = $count + 1;
            $stmt->close();
            $stmt = $this->conn->prepare("UPDATE answers SET `count` = ? WHERE id = ?");
            $stmt->bind_param("ii", $count, $answer_id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            $answer = $data["answer"];
            $stmt = $this->conn->prepare("INSERT INTO answers (question_id, type_id, answer, count, correct) VALUES (?, ?, ?, 1, ?)");
            $stmt->bind_param("iisi", $question_id, $type_id, $answer, $correct);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return true;
        }

    }

    public function updateQuestion($question_id, $data)
    {
        $question = $data["question"];
        $subject = $data["subject"];
        $type = $data["type"];
        if ($type != "open_ended" && $type != "multiple_choice") {
            return false;
        }
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $this->isUserAuthorized($accessToken, $question_id))) {
            $query = "SELECT id FROM types WHERE type = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $result = $stmt->get_result();
            $type_id = $result->fetch_assoc();
            $type_id = $type_id['id'];
            $stmt->close();

            if (!$type_id) {
                return false;
            }

            $stmt = $this->conn->prepare("UPDATE questions SET question = ?, subject = ?, type_id = ? WHERE id = ?");
            $stmt->bind_param("ssii", $question, $subject, $type_id, $question_id);
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
    public function updateAnswer($answer_id, $data)
    {
        $answer = $data["answer"];
        $correct = $data["correct"];
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $this->isUserAuthorizedForAnswer($accessToken, $answer_id))) {
            $stmt = $this->conn->prepare("UPDATE answers SET answer = ?, correct = ? WHERE id = ?");
            $stmt->bind_param("sii", $answer, $correct, $answer_id);
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
    public function deleteQuestion($question_id, $data)
    {
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $this->isUserAuthorized($accessToken, $question_id))) {
            $stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $affectedRows > 0;
        } else {
            return false;
        }
    }
    public function deleteAnswer($answer_id, $data)
    {
        $accessToken = $data['access_token'];
        $accessTokenData = $this->checkAcessToken($accessToken);
        $role = $accessTokenData['role'];
        if (($role === 'admin') || ($role === 'user' && $this->isUserAuthorizedForAnswer($accessToken, $answer_id))) {
            $stmt = $this->conn->prepare("DELETE FROM answers WHERE id = ?");
            $stmt->bind_param("i", $answer_id);
            $stmt->execute();
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $affectedRows > 0;
        } else {
            return false;
        }
    }
}

