<?php

namespace App\Livewire;

use App\Models\AIChat;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AIChat extends Component
{
    public $message = '';
    public $isOpen = false;
    public $isTyping = false;
    public $conversations = [];
    public $currentContext = '';

    protected $listeners = ['refreshChat'];

    public function mount()
    {
        $this->loadConversations();
        $this->currentContext = $this->getCurrentPageContext();
    }

    public function loadConversations()
    {
        $this->conversations = AIChat::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values()
            ->toArray();
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->currentContext = $this->getCurrentPageContext();
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userMessage = trim($this->message);
        $this->message = '';
        $this->isTyping = true;

        // Add user message to conversations
        $this->conversations[] = [
            'message' => $userMessage,
            'response' => '',
            'is_user' => true,
            'created_at' => now()
        ];

        // Process with AI
        $response = $this->processWithAI($userMessage);

        // Add AI response to conversations
        $this->conversations[] = [
            'message' => '',
            'response' => $response['text'],
            'is_user' => false,
            'action_type' => $response['action_type'] ?? null,
            'action_data' => $response['action_data'] ?? null,
            'response_type' => $response['response_type'] ?? 'text',
            'created_at' => now()
        ];

        // Save to database
        AIChat::create([
            'user_id' => Auth::id(),
            'message' => $userMessage,
            'response' => $response['text'],
            'context' => $this->currentContext,
            'action_type' => $response['action_type'] ?? null,
            'action_data' => $response['action_data'] ?? null,
            'response_type' => $response['response_type'] ?? 'text',
            'is_processed' => true,
        ]);

        $this->isTyping = false;

        // Execute action if needed
        if (isset($response['action_type']) && $response['action_type'] === 'navigation') {
            $this->dispatch('navigateTo', $response['action_data']);
        }
    }

    private function processWithAI($message)
    {
        // Simple AI logic - you can replace this with actual AI service
        $message = strtolower($message);
        
        // Navigation patterns
        if (str_contains($message, 'görev') || str_contains($message, 'task')) {
            if (str_contains($message, 'oluştur') || str_contains($message, 'create')) {
                return [
                    'text' => 'Görev oluşturma sayfasına yönlendiriyorum...',
                    'action_type' => 'navigation',
                    'action_data' => ['route' => 'admin.tasks.create'],
                    'response_type' => 'action'
                ];
            }
            return [
                'text' => 'Görevler sayfasına yönlendiriyorum...',
                'action_type' => 'navigation',
                'action_data' => ['route' => 'admin.tasks'],
                'response_type' => 'action'
            ];
        }

        if (str_contains($message, 'kullanıcı') || str_contains($message, 'user')) {
            return [
                'text' => 'Kullanıcılar sayfasına yönlendiriyorum...',
                'action_type' => 'navigation',
                'action_data' => ['route' => 'admin.users'],
                'response_type' => 'action'
            ];
        }

        if (str_contains($message, 'randevu') || str_contains($message, 'appointment')) {
            return [
                'text' => 'Randevular sayfasına yönlendiriyorum...',
                'action_type' => 'navigation',
                'action_data' => ['route' => 'admin.appointments'],
                'response_type' => 'action'
            ];
        }

        // Help patterns
        if (str_contains($message, 'yardım') || str_contains($message, 'help')) {
            return [
                'text' => 'Size nasıl yardımcı olabilirim? Şunları yapabilirim:
• Görev oluşturma ve yönetme
• Kullanıcı yönetimi
• Randevu takibi
• Sayfa yönlendirmeleri

Ne yapmak istiyorsunuz?',
                'response_type' => 'text'
            ];
        }

        // Setup patterns
        if (str_contains($message, 'kurulum') || str_contains($message, 'setup')) {
            return [
                'text' => 'Kurulum konusunda size yardımcı olabilirim. Hangi konuda sorun yaşıyorsunuz?',
                'response_type' => 'text'
            ];
        }

        // Default response
        return [
            'text' => 'Anladım. Size nasıl yardımcı olabilirim? Görevler, kullanıcılar, randevular hakkında soru sorabilir veya yardım isteyebilirsiniz.',
            'response_type' => 'text'
        ];
    }

    private function getCurrentPageContext()
    {
        $route = request()->route();
        if (!$route) return '';

        $routeName = $route->getName();
        $context = '';

        if (str_contains($routeName, 'admin')) {
            $context = 'Admin Panel';
        } elseif (str_contains($routeName, 'personel')) {
            $context = 'Personel Panel';
        }

        return $context;
    }

    public function refreshChat()
    {
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.a-i-chat');
    }
}
