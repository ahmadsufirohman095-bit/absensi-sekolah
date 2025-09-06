<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifikasi Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-end mb-4">
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-blue-700 transition">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    </div>

                    @forelse ($notifications as $notification)
                        <div class="p-4 mb-4 border rounded-lg {{ $notification->read_at ? 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600' : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' }}">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-lg">{{ $notification->data['message'] ?? 'Pesan Notifikasi' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Dikirim oleh: {{ $notification->data['action_by'] ?? 'Sistem' }} pada {{ $notification->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @unless($notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 transition">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-600 dark:text-gray-400">Tidak ada notifikasi.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
