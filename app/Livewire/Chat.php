<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class Chat extends Component 
{
    public $users;
    public $selectedUser;
    public $newMessage = '';
    public $messages = [];
    public $loginId;

   public function mount()
{
    
     $this->loginId = Auth::id();
    
    // Convert to array to avoid validation conflicts
    $this->users = User::where('id', '!=', $this->loginId)->latest()->get()->all();
    
    if (count($this->users) > 0) {
        $this->selectedUser = User::find($this->users[0]['id']);
        $this->loadMessages();
    }
}

public function loadMessages()
{
    if (!$this->selectedUser) return;

    $this->messages = ChatMessage::query()
        ->where(function($q) {
            $q->where("sender_id", $this->loginId)
              ->where("receiver_id", $this->selectedUser->id);
        })
        ->orWhere(function($q) {
            $q->where("sender_id", $this->selectedUser->id)
              ->where("receiver_id", $this->loginId);
        })
        ->oldest()
        ->get()
        ->all(); // Use ->all() to pass a standard array to the property
}

    public function selectUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->selectedUser = $user;
            $this->loadMessages();
        }
    }

  public function submit()
{
    $this->validate(['newMessage' => 'required|string|max:1000']);

    $message = ChatMessage::create([
        "sender_id" => auth()->id(),
        "receiver_id" => $this->selectedUser['id'],
        "message" => $this->newMessage
    ]);

    // Push to your own UI immediately
    $this->messages[] = $message->toArray();
    $this->newMessage = '';

    // Broadcast to Reverb (this sends to EVERYONE on the channel)
    broadcast(new MessageSent($message));
}

    

    /**
     * Listen for Echo events.
     * Ensure your MessageSent event implements ShouldBroadcast
     */
    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},MessageSent" => "newChatMessageNotification"
        ];
    }

   // --- Fixed Code ---
public function updatedNewMessage($value)
{
    $this->dispatch(
        'userTyping', 
        userID: $this->loginId, // Fixed lowercase 'd'
        userName: Auth::id(), 
        selectedUserID: $this->selectedUser['id']
    );
}
    

public function newChatMessageNotification($event)
{
    // Since broadcastWith() returns a flat array, $event IS the message data
    $incoming = $event;

    // 1. Safety check to ensure we have the data we expect
    if (!isset($incoming['sender_id'])) {
        return;
    }

    // 2. Ignore if I sent it (to avoid duplicates)
    if ($incoming['sender_id'] == auth()->id()) {
        return;
    }

    // 3. Only push if the message belongs to the current conversation
    if ($this->selectedUser && $incoming['sender_id'] == $this->selectedUser['id']) {
        $this->messages[] = $incoming;
    }
}

    #[Title('Chat')]
    public function render()
    {
        return view('livewire.chat')->layout('layouts.app');
    }
}