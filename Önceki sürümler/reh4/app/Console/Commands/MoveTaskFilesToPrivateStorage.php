<?php

namespace App\Console\Commands;

use App\Models\TaskFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveTaskFilesToPrivateStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:move-to-private {--dry-run : Sadece kontrol et, taşıma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task dosyalarını public storage\'dan private storage\'a taşır';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN: Sadece kontrol edilecek, dosyalar taşınmayacak.');
        }
        
        $taskFiles = TaskFile::all();
        $movedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        $this->info("Toplam {$taskFiles->count()} dosya kontrol edilecek...");
        
        foreach ($taskFiles as $taskFile) {
            $this->line("İşleniyor: {$taskFile->file_name}");
            
            // Dosya zaten private storage'da mı?
            if (Storage::disk('local')->exists($taskFile->file_path)) {
                $this->comment("  Zaten private storage'da: {$taskFile->file_path}");
                $skippedCount++;
                continue;
            }
            
            // Public storage'da var mı?
            if (!Storage::disk('public')->exists($taskFile->file_path)) {
                $this->error("  Dosya bulunamadı: {$taskFile->file_path}");
                $errorCount++;
                continue;
            }
            
            if (!$dryRun) {
                try {
                    // Dosyayı private storage'a kopyala
                    $contents = Storage::disk('public')->get($taskFile->file_path);
                    Storage::disk('local')->put($taskFile->file_path, $contents);
                    
                    // Kopyalama başarılı mı kontrol et
                    if (Storage::disk('local')->exists($taskFile->file_path)) {
                        // Public storage'dan sil
                        Storage::disk('public')->delete($taskFile->file_path);
                        $this->info("  ✓ Taşındı: {$taskFile->file_path}");
                        $movedCount++;
                    } else {
                        $this->error("  ✗ Private storage'a kopyalanamadı: {$taskFile->file_path}");
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ✗ Hata: {$e->getMessage()}");
                    $errorCount++;
                }
            } else {
                $this->info("  Taşınacak: {$taskFile->file_path}");
                $movedCount++;
            }
        }
        
        $this->newLine();
        $this->info('ÖZET:');
        
        if ($dryRun) {
            $this->info("  Taşınacak: {$movedCount}");
        } else {
            $this->info("  Taşınan: {$movedCount}");
        }
        
        $this->info("  Atlanan: {$skippedCount}");
        
        if ($errorCount > 0) {
            $this->error("  Hatalı: {$errorCount}");
        }
        
        if ($dryRun) {
            $this->newLine();
            $this->comment('Gerçek taşıma için --dry-run parametresini kaldırın:');
            $this->comment('php artisan files:move-to-private');
        }
        
        return Command::SUCCESS;
    }
} 