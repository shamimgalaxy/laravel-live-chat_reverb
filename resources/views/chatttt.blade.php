<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Simple Chat Placeholder -->
                <div class="space-y-4 h-64 overflow-y-auto mb-4 border-b pb-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500 italic">No messages yet...</div>
                </div>

                <div class="flex gap-2">
                    <input type="text" placeholder="Type your message..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white">
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Send
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>