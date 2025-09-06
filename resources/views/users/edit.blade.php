<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit User') }}</h2>
    </x-slot>
    <turbo-frame id="main-content">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data" data-turbo-frame="_top">
                            @method('PUT')
                            @include('users._form', ['user' => $user])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </turbo-frame>
</x-app-layout>
