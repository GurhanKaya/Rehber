<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Task;
use Carbon\Carbon;

class Home extends Component
{
    public function render()
    {
        // Kullanıcı istatistikleri
        $totalUsers = User::count();
        $totalPersonel = User::where('role', 'personel')->count();
        $totalAdmins = User::where('role', 'admin')->count();

        // Randevu istatistikleri
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'bekliyor')->count();
        $approvedAppointments = Appointment::where('status', 'onaylandı')->count();
        $completedAppointments = Appointment::where('status', 'yapıldı')->count();
        $todayAppointments = Appointment::whereDate('date', Carbon::today())->count();

        // Görev istatistikleri
        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'bekliyor')->count();
        $inProgressTasks = Task::where('status', 'devam ediyor')->count();
        $completedTasks = Task::where('status', 'tamamlandı')->count();
        $cancelledTasks = Task::where('status', 'iptal')->count();
        $publicTasks = Task::where('type', 'public')->count();
        $cooperativeTasks = Task::where('type', 'cooperative')->count();
        $assignedTasks = Task::where('type', 'assigned')->count();

        // Bugünkü aktiviteler
        $todayDeadlineTasks = Task::whereDate('deadline', Carbon::today())
            ->where('status', '!=', 'tamamlandı')
            ->where('status', '!=', 'iptal')
            ->count();

        $upcomingTasks = Task::where('deadline', '>', Carbon::today())
            ->where('status', '!=', 'tamamlandı')
            ->where('status', '!=', 'iptal')
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $recentAppointments = Appointment::with('appointmentSlot.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTasks = Task::with('assignedUser')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.admin.home', [
            // Kullanıcı istatistikleri
            'totalUsers' => $totalUsers,
            'totalPersonel' => $totalPersonel,
            'totalAdmins' => $totalAdmins,
            
            // Randevu istatistikleri
            'totalAppointments' => $totalAppointments,
            'pendingAppointments' => $pendingAppointments,
            'approvedAppointments' => $approvedAppointments,
            'completedAppointments' => $completedAppointments,
            'todayAppointments' => $todayAppointments,
            
            // Görev istatistikleri
            'totalTasks' => $totalTasks,
            'pendingTasks' => $pendingTasks,
            'inProgressTasks' => $inProgressTasks,
            'completedTasks' => $completedTasks,
            'cancelledTasks' => $cancelledTasks,
            'publicTasks' => $publicTasks,
            'cooperativeTasks' => $cooperativeTasks,
            'assignedTasks' => $assignedTasks,
            'todayDeadlineTasks' => $todayDeadlineTasks,
            
            // Son aktiviteler
            'upcomingTasks' => $upcomingTasks,
            'recentAppointments' => $recentAppointments,
            'recentTasks' => $recentTasks,
        ])->layout('layouts.admin');
    }
} 