<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AppointmentSlot;
use App\Models\Appointment;
use App\Models\Task;
use Carbon\Carbon;

class Home extends Component
{
    public function render()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        
        // Randevu istatistikleri
        $recentSlots = AppointmentSlot::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        
        // Bugünkü randevular
        $todayAppointments = Appointment::whereHas('appointmentSlot', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->whereDate('appointment_date', Carbon::today())->count();
        
        // Bekleyen randevular - Admin tarafından eklenen randevuları da dahil et
        $pendingAppointments = Appointment::whereHas('appointmentSlot', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('status', 'bekliyor')->count();
        
        // Public atanmamış görevler
        $publicUnassignedTasks = Task::where('type', 'public')
            ->whereNull('assigned_user_id')
            ->where('status', '!=', 'tamamlandı')
            ->where('status', '!=', 'iptal')
            ->count();
        
        // Görev istatistikleri
        $myTasks = Task::where('assigned_user_id', $userId)->count();
        $cooperativeTasks = Task::where('type', 'cooperative')
            ->whereHas('participants', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();
        $totalTasks = $myTasks + $cooperativeTasks;
        
        // Bekleyen görevler
        $pendingTasks = Task::where(function($query) use ($userId) {
            $query->where('assigned_user_id', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })->where('status', 'bekliyor')->count();
        
        // Devam eden görevler
        $inProgressTasks = Task::where(function($query) use ($userId) {
            $query->where('assigned_user_id', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })->where('status', 'devam ediyor')->count();
        
        // Aktif görevler (bekleyen + devam eden)
        $activeTasks = $pendingTasks + $inProgressTasks;
        
        // Yaklaşan görevler (3 gün içinde deadline)
        $upcomingTasks = Task::where(function($query) use ($userId) {
            $query->where('assigned_user_id', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })->where('status', '!=', 'tamamlandı')
          ->where('status', '!=', 'iptal')
          ->where('deadline', '>=', Carbon::now())
          ->where('deadline', '<=', Carbon::now()->addDays(3))
          ->orderBy('deadline')
          ->take(5)
          ->get();
        
        // Bugün deadline'ı olan görevler
        $todayDeadlineTasks = Task::where(function($query) use ($userId) {
            $query->where('assigned_user_id', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })->where('status', '!=', 'tamamlandı')
          ->where('status', '!=', 'iptal')
          ->whereDate('deadline', Carbon::today())
          ->count();
        
        return view('livewire.personel.home', [
            'recentSlots' => $recentSlots,
            'todayAppointments' => $todayAppointments,
            'pendingAppointments' => $pendingAppointments,
            'publicUnassignedTasks' => $publicUnassignedTasks,
            'activeTasks' => $activeTasks,
            'totalTasks' => $totalTasks,
            'upcomingTasks' => $upcomingTasks,
            'todayDeadlineTasks' => $todayDeadlineTasks,
        ])->layout('layouts.personel');
    }
}
