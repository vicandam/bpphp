<section>
    @if (session('status'))
        <div class="mb-4 text-green-500">
            {{ session('status') }}
        </div>
    @endif
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('GHL Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your GHL Settings.") }}
        </p>
    </header>

    <form method="post" action="{{ route('ghl.settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="name" :value="__('GHL API Key')" />
            <x-text-input id="name" name="ghl_api_key" type="text" class="mt-1 block w-full" :value="old('ghl_api_key', $user->ghl_api_key)" required autofocus autocomplete="ghl_api_key" />
            <x-input-error class="mt-2" :messages="$errors->get('ghl_api_key')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="ghl_location_id" type="text" class="mt-1 block w-full" :value="old('ghl_location_id', $user->ghl_location_id)" required autocomplete="ghl_location_id" />
            <x-input-error class="mt-2" :messages="$errors->get('ghl_location_id')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
