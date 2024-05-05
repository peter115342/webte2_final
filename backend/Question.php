<?php

require_once "vendor/phpqrcode/qrlib.php";

class Question
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllQuestionAnswers($question_id)
    {
        $query = "SELECT answer, count FROM answers WHERE question_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = mysqli_fetch_assoc($result)) {
            $answers[] = $row;
        }
        $stmt->close();
        return $answers;
    }

    public function getQuestionsByUserId($id)
    {
        $query = "SELECT questions.date, 
        questions.user_id , questions.subject , questions.question , questions.type_id, questions.code, 
        questions.qr_code, answers.question_id, answers.answer, answers.correct, answers.count, types.type
        FROM questions 
        LEFT JOIN answers ON answers.question_id = questions.id
        LEFT JOIN types ON answers.type_id = types.id
        WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = mysqli_fetch_assoc($result)) {
            $questions[] = $row;
        }
        $stmt->close();
        return $questions;
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
    public function getQuestionById($id)
    {
        $query = "SELECT * FROM questions WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $question = $result->fetch_assoc();
        $stmt->close();
        return $question;
    }
    public function getQuestionByQR($qr)
    {
        $query = "SELECT * FROM questions WHERE qr_code = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $qr_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $question = $result->fetch_assoc();
        $stmt->close();
        return $question;
    }



    public function createQuestion($data)
    {
        $question = $data["question"];
        $subject = $data["subject"];
        $type = $data["type"];
        $user_id = $data["user_id"];
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
        $url = "https://example.com/api/endpoint";   // TODO: Doplnit spravnu URL
        $dir = "qr_codes/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $qrcode_filename = $dir . "qr_code_" . uniqid() . ".png";
        QRcode::png($url, $qrcode_filename);

        $query = "SELECT id FROM types WHERE type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $type_id = $result->fetch_assoc();
        $stmt->close();
        if(!$type_id){
            return false;
        }
        $stmt = $this->conn->prepare("INSERT INTO questions (question, subject, type_id, user_id, code, qr_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $question, $subject, $type_id, $user_id, $code, $qrcode_filename);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return true;

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
        if(!$type_id){
            return false;
        }
        $query = "SELECT id FROM answers WHERE answer = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $answer);
        $stmt->execute();
        $result = $stmt->get_result();
        $answer_id = $result->fetch_assoc();
        $answer_id = $answer_id['id'];
        $stmt->close();
        if ($answer_id) {
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

    public function updateQuestion($id, $data)
    {
        $question = $data["question"];
        $subject = $data["subject"];
        $type = $data["type"];

        $query = "SELECT id FROM types WHERE type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $type_id = $result->fetch_assoc();
        $type_id = $type_id['id'];
        $stmt->close();

        if(!$type_id){
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE questions SET question = ?, subject = ?, type_id = ? WHERE id = ?");
        $stmt->bind_param("ssii", $question, $subject, $type_id, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
    public function updateAnswer($id, $data)
    {
        $answer = $data["answer"];
        $correct = $data["correct"];
        $stmt = $this->conn->prepare("UPDATE answers SET (answer = ?, correct = ?) WHERE id = ?");
        $stmt->bind_param("sii", $answer, $correct, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
    public function deleteQuestion($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows > 0;
    }
    public function deleteAnswer($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM answers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows > 0;
    }
}

