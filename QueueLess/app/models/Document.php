<?php

class Document {

    private mysqli $db;

    private array $requiredDocs = [
        'birth_certificate',
        'report_card',
        'good_moral'
    ];

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    public function upload(int $enrollmentId, string $type, string $path): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO documents (enrollment_id, document_type, file_path)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE file_path = VALUES(file_path)
        ");

        $stmt->bind_param("iss", $enrollmentId, $type, $path);
        return $stmt->execute();
    }

    public function getUploaded(int $enrollmentId): array
    {
        $stmt = $this->db->prepare("
            SELECT document_type FROM documents WHERE enrollment_id = ?
        ");
        $stmt->bind_param("i", $enrollmentId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return array_column($result, 'document_type');
    }

    public function getMissing(int $enrollmentId): array
    {
        $uploaded = $this->getUploaded($enrollmentId);

        return array_values(array_diff($this->requiredDocs, $uploaded));
    }
}