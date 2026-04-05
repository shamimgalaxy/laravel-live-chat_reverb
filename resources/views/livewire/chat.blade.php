<div> <x-layouts.app>
        <div class="p-6">
            <div class="relative mb-6 w-full">
                <flux:heading size="xl" level="1">Chat</flux:heading>
                <flux:subheading size="lg" class="mb-6">Manage your profile and account settings</flux:subheading>
                <flux:separator variant="subtle" />
            </div>

            <div class="flex h-[550px] text-sm border rounded-xl shadow overflow-hidden bg-white">
                <div class="w-1/4 border-r bg-gray-50">
                    <div class="p-4 font-bold text-gray-700 border-b">Users</div>
                    <div class="divide-y">
                        @foreach($users as $user)
                            <div 
                                wire:click="selectUser({{ $user->id }})"
                                class="p-4 cursor-pointer hover:bg-gray-100 flex items-center {{ ($selectedUser && $selectedUser->id === $user->id) ? 'bg-blue-50' : '' }}"
                            >
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            
                <div class="w-3/4 flex flex-col">
                    <div class="p-4 border-b bg-gray-50">
                        @if($selectedUser)
                            <div class="text-lg font-semibold text-gray-800">{{ $selectedUser->name }}</div>
                            <div class="text-xs text-gray-500">{{ $selectedUser->email }}</div>
                        @else
                            <div class="text-lg font-semibold text-gray-800">Select a user</div>
                        @endif
                    </div>
            
                    <div class="flex-1 p-4 overflow-y-auto space-y-2 bg-gray-50">
            @foreach ($messages as $message)
    <div class="flex {{ $message['sender_id'] === Auth::id() ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-xs px-4 py-2 rounded-2xl shadow 
            {{ $message['sender_id'] === Auth::id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
            
            {{ $message['message'] }}
            
        </div>
    </div>
@endforeach
                    </div>

                    <div id="typing-indicator" class="px-4 pb-1 text-xs text-gray-400 italic"></div>
            
                    <form wire:submit="submit" class="p-4 border-t bg-white flex items-center gap-2">
                        <input 
                        wire:model.live="newMessage"
                            type="text"
                            class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="Type your message..." />
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-layouts.app>
</div> 

<script>
    document.addEventListener('livewire:init', () => {
        // 1. Listen for the event coming FROM your Livewire Component
        Livewire.on('userTyping', (event) => {
            // Reverb whisper to the selected user
            window.Echo.private(`chat.${event.selectedUserID}`)
                .whisper('typing', {
                    userID: event.userID,
                    userName: event.userName
                });
        });

        // 2. Listen for whispers coming FROM the other user
        let typingTimer;
        window.Echo.private(`chat.{{ $loginId }}`)
            .listenForWhisper('typing', (event) => {
                const indicator = document.getElementById("typing-indicator");
                
                if (indicator) {
                    indicator.innerText = `${event.userName} is typing...`;

                    // Clear the message after 2 seconds of no whispers
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        indicator.innerText = '';
                    }, 2000);
                }
            });
    });
</script>

