<?php

namespace App\Http\Controllers;

use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class TaskFileController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Dosyayı web'de görüntüle (admin-only, route middleware ile korunur)
     */
    public function view(TaskFile $taskFile)
    {
        return $this->serveFileForView($taskFile);
    }
    
    /**
     * Güvenli dosya indirme (middleware ile kimlik/doğrulama kontrolü yapılır)
     */
    public function download(TaskFile $taskFile)
    {
        return $this->serveFile($taskFile);
    }
    
    /**
     * Dosyayı web'de görüntülemek için sun
     */
    private function serveFileForView(TaskFile $taskFile)
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
        
        // Web'de görüntülenebilir dosya türleri
        $viewableTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'text/plain', 'application/pdf'];
        
        if (in_array($mimeType, $viewableTypes)) {
            // Web'de görüntülenebilir dosyalar için inline response
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } else {
            // Görüntülenemeyen dosyalar için indirme
            return response()->download($filePath, $taskFile->file_name, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        }
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