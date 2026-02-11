<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('rep-user', $user) }}">
        @csrf
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="'كلمه السر الجديده'" />

            <input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />
        </div>


        <div class="flex items-center justify-end mt-4">


            <x-primary-button class="ml-3">

تحديث البيانات
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
