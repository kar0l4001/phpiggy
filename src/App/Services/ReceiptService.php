<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;
use Exception;

class ReceiptService
{
    public function __construct(private Database $db)
    {
    }

    public function validateFile(?array $file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException([
                'receipt' => 'Failed to upload file'
            ]);
        }

        $maxFileSizeMB = 5;

        if ($file['size'] / 1024 / 1024 > $maxFileSizeMB) {
            throw new ValidationException([
                'receipt' => 'Uploaded file size is too large'
            ]);
        }

        $originalFileName = $file['name'];

        if (!preg_match('/^[A-za-z0-9\s._-]+$/', $originalFileName)) {
            throw new ValidationException([
                'receipt' => 'Invalid filename'
            ]);
        }

        $fileMimeType = $file['type'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            throw new ValidationException([
                'receipt' => 'Invalid file type'
            ]);
        }
    }

    public function upload(int $transactionId, array $file)
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = bin2hex(random_bytes(16)) . '.' . $fileExtension;

        $uploadPath = Paths::STORAGE_UPLOADS . '/' . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new ValidationException(['receipt' => 'Failed to upload file']);
        }

        $fileName = addslashes(Paths::STORAGE_UPLOADS . '/' . $newFileName);

        $this->db->query("INSERT INTO receipts(transaction_id, original_filename, storage_filename, media_type)
        VALUES (:transaction_id, :original_filename, :storage_filename, :media_type)", [
            'transaction_id' => $transactionId,
            'original_filename' => $file['name'],
            'storage_filename' => $fileName,
            'media_type' => $file['type']
        ]);
    }

    public function getReceipt(string $id)
    {
        $receipt = $this->db->query("SELECT * FROM receipts WHERE id = :id", ['id' => $id])->find();

        return $receipt;
    }

    public function read(array $receipt)
    {
        $filePath = $receipt['storage_filename'];

        if (!file_exists($filePath)) {
            redirectTo('/');
        }

        header("Content-Disposition: inline;filename={$receipt['original_filename']}",);
        header("Content-Type: {$receipt['media_type']}");

        readfile($filePath);
    }

    public function delete(array $receipt)
    {
        $filePath = $receipt['storage_filename'];

        unlink($filePath);

        $this->db->query("DELETE FROM receipts WHERE id = :id", ['id' => $receipt['id']]);
    }
}

/*
do tej implementacji tabela receipts: 
id bigint
name varchar
type varchar
file longblob

$createReceiptQuery = "INSERT INTO receipts(name, type, file) values(:name, :type, :path)";
        $createReceiptParams = [
            'name' => $file['name'],
            'type' => $file['type'],
            'path' => file_get_contents($fileName)
        ];
        $updateTransactionsQuery = "UPDATE transactions SET receipt_id = :receipt_id
            WHERE id = :id
            AND user_id = :user_id";
        $updateTransactionParams = [
            'id' => $id,
            'user_id' => $_SESSION['user']
        ];

        try {
            $this->db->startTransaction();
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0", []);
            $currentTransactionReceiptId = $this->db->query(
                "SELECT receipt_id FROM transactions WHERE id = :id",
                ['id' => $id]
            )->fetchAll()[0]['receipt_id'];
            $this->db->query("DELETE FROM receipts WHERE id = :id", ['id' => $currentTransactionReceiptId]);
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1", []);
            $this->db->query($createReceiptQuery, $createReceiptParams);
            $receipt_id = (int) $this->db->id();
            $updateTransactionParams['receipt_id'] = $receipt_id;
            $this->db->query($updateTransactionsQuery, $updateTransactionParams);
            $this->db->endTransaction();
        } catch (Exception $e) {
            $this->db->rollback();
            throw new ValidationException(['receipt' => 'SQL error']);
        }
*/
