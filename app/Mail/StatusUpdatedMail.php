<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class StatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $documents;

    public function __construct($data, $documents = []) {
        $this->data = $data;
        $this->documents = $documents;
    }

    public function build() {
        $mail = $this->subject('Your Valuation Request Status Updated')
                    ->view('emails.status_updated')
                    ->with([
                        'data' => $this->data, 
                        'documents' => $this->documents,
                        'title' => 'Status Update'
                    ]);

        // Attach file documents and prepare text documents
        $textDocuments = [];
        foreach ($this->documents as $document) {
            if (isset($document['is_file']) && $document['is_file'] && isset($document['file_path'])) {
                // It's a file, attach it
                // Try different disk configurations to find the file
                $filePath = null;
                $disks = ['local', 'public'];
                
                foreach ($disks as $disk) {
                    $potentialPath = Storage::disk($disk)->path($document['file_path']);
                    if (file_exists($potentialPath)) {
                        $filePath = $potentialPath;
                        break;
                    }
                }
                
                // If still not found, try storage_path('app') directly
                if (!$filePath) {
                    $directPath = storage_path('app/' . $document['file_path']);
                    if (file_exists($directPath)) {
                        $filePath = $directPath;
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
                            'application/pdf' => 'pdf',
                            'application/msword' => 'doc',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                        ];
                        $extension = $mimeToExt[$document['file_type']] ?? 'pdf';
                    }
                    $attachmentName = $fileName . '.' . $extension;
                    $mail->attach($filePath, ['as' => $attachmentName]);
                }
            } else if (isset($document['text_value'])) {
                // It's a text document, collect for display in email
                $textDocuments[] = $document;
            }
        }

        // Pass text documents to view
        return $mail->with(['textDocuments' => $textDocuments]);
    }
}
