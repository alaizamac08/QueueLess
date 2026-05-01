<?php
class Student
{
    private mysqli $conn;
    private string $table = 'students';

    public function __construct(mysqli $db)
    {
        $this->conn = $db;
    }

    public function createStudent(array $data): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
                (lrn, first_name, middle_name, last_name, suffix,
                sex, birth_date, age, place_of_birth, nationality,
                address, phone_number, email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'issssssisssss',
            $data['lrn'],
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['suffix'],
            $data['sex'],
            $data['birth_date'],
            $data['age'],
            $data['place_of_birth'],
            $data['nationality'],
            $data['address'],
            $data['phone_number'],
            $data['email']
        );

        return $stmt->execute();
    }

    public function getStudentById(int $studentId, bool $full = false): ?array
    {
        $columns = $full
            ? 'student_id, lrn, first_name, middle_name, last_name,
            suffix, sex, birth_date, age, place_of_birth,
            nationality, address, phone_number, email'
            : 'student_id, lrn, first_name, middle_name, last_name,
            suffix, sex, birth_date, email, phone_number';

        $stmt = $this->conn->prepare(
            "SELECT {$columns} FROM {$this->table} WHERE student_id = ?"
        );
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function searchByName(string $lastName, int $limit = 20): array
    {
        $like  = $lastName . '%';
        $stmt  = $this->conn->prepare("
            SELECT student_id, lrn, first_name, middle_name, last_name, suffix
            FROM {$this->table}
            WHERE last_name LIKE ?
            ORDER BY last_name, first_name
            LIMIT ?
        ");
        $stmt->bind_param('si', $like, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteStudent(int $studentId): bool
    {
        $stmt = $this->conn->prepare("
            DELETE FROM {$this->table}
            WHERE student_id = ?
        ");

        $stmt->bind_param('i', $studentId);

        return $stmt->execute();
    }
}
