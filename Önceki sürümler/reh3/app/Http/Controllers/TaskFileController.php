<?php

namespace App\Http\Controllers;

use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TaskFileController extends Controller
{
    /**
     * Güvenli dosya indirme
     */
    public function download(TaskFile $taskFile)
    {
        // Doğrudan URL erişimini engelle - her zaman 403 hatası ver
        abort(403, 'Not Authorized - Bu dosyaya doğrudan erişim engellenmiştir.');
    }
    
    /**
     * Dosyayı güvenli bir şekilde sun
     */
    private function serveFile(TaskFile $taskFile)
    {
        // Önce public storage'da kontrol et (eski dosyalar için)
        if (Storage::disk('public')->exists($taskFile->file_path)) {
            $filePath = Storage::disk('public')->path($taskFile->file_path);
            $mimeType = $taskFile->mime_type;
        }
        // Sonra private storage'da kontrol et (yeni dosyalar için)
        elseif (Storage::disk('local')->exists($taskFile->file_path)) {
            $filePath = Storage::disk('local')->path($taskFile->file_path);
            $mimeType = $taskFile->mime_type;
        }
        else {
            abort(404, 'Dosya bulunamadı.');
        }
        
        if (!file_exists($filePath)) {
            abort(404, 'Dosya bulunamadı.');
        }
        
        return response()->download($filePath, $taskFile->file_name, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
} 