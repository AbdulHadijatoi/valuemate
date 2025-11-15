<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $recipientType;
    public $documents;

    public function __construct($data, $recipientType = 'user', $documents = []) {
        $this->data = $data;
        $this->recipientType = $recipientType; // 'user', 'company', or 'admin'
        $this->documents = $documents;
    }

    public function build() {
        $mail = $this->subject('Valuation Payment Successful')
                    ->view('emails.payment_success')
                    ->with([
                        'data' => $this->data,
                        'documents' => $this->documents,
                        'recipientType' => $this->recipientType,
                        'title' => 'Payment Successful'
                    ]);

        // Attach file documents and prepare text documents
        $textDocuments = [];
        
        \Log::info('PaymentSuccessMail - Documents count: ' . count($this->documents));
        \Log::info('PaymentSuccessMail - Documents data: ' . json_encode($this->documents));
        
        foreach ($this->documents as $document) {
            // Handle file documents
            // Check is_file as both boolean and integer (database might store 1/0)
            $isFile = isset($document['is_file']) && ($document['is_file'] === true || $document['is_file'] === 1 || $document['is_file'] === '1');
            
            if ($isFile && isset($document['file_path']) && !empty($document['file_path'])) {
                // It's a file, attach it
                $filePath = null;
                
                // First, try using Storage facade with default disk
                try {
                    $defaultDisk = config('filesystems.default', 'local');
                    $potentialPath = Storage::disk($defaultDisk)->path($document['file_path']);
                    if (file_exists($potentialPath)) {
                        $filePath = $potentialPath;
                    }
                } catch (\Exception $e) {
                    // Continue to next method
                }
                
                // Try different disk configurations if default didn't work
                if (!$filePath) {
                    $disks = ['local', 'public'];
                    foreach ($disks as $disk) {
                        try {
                            $potentialPath = Storage::disk($disk)->path($document['file_path']);
                            if (file_exists($potentialPath)) {
                                $filePath = $potentialPath;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                
                // If still not found, try direct paths
                if (!$filePath) {
                    $directPaths = [
                        storage_path('app/' . $document['file_path']),
                        storage_path('app/public/' . $document['file_path']),
                    ];
                    
                    foreach ($directPaths as $directPath) {
                        if (file_exists($directPath)) {
                            $filePath = $directPath;
                            break;
                        }
                    }
                }
                
                if ($filePath && file_exists($filePath)) {
                    $fileName = $document['document_name'] ?? 'document';
                    // Sanitize filename for attachment
                    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);
                    // Get file extension from the stored file or use a default
                    $extension = pathinfo($document['file_path'], PATHINFO_EXTENSION);
                    if (!$extension && isset($document['file_type'])) {
                        $mimeToExt = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'application/pdf' => 'pdf',
                            'application/msword' => 'doc',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                            'application/vnd.ms-excel' => 'xls',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                        ];
                        $extension = $mimeToExt[$document['file_type']] ?? 'pdf';
                    }
                    if (!$extension) {
                        $extension = 'pdf'; // Default extension
                    }
                    $attachmentName = $fileName . '.' . $extension;
                    $mail->attach($filePath, ['as' => $attachmentName]);
                    \Log::info('PaymentSuccessMail - Attached file: ' . $filePath . ' as ' . $attachmentName);
                } else {
                    \Log::warning('PaymentSuccessMail - File not found: ' . ($document['file_path'] ?? 'no path'));
                }
            } 
            
            // Handle text documents (check separately as a document can have both file and text)
            if (isset($document['text_value']) && $document['text_value'] !== null && $document['text_value'] !== '') {
                // It's a text document, collect for display in email
                $textDocuments[] = $document;
                \Log::info('PaymentSuccessMail - Added text document: ' . ($document['document_name'] ?? 'unnamed'));
            }
        }
        
        \Log::info('PaymentSuccessMail - Text documents count: ' . count($textDocuments));

        // Pass text documents to view
        return $mail->with(['textDocuments' => $textDocuments]);
    }
}
