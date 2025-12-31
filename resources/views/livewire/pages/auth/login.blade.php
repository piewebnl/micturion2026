<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<x-cards.card>

    <x-auth.session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">

        <x-forms.input :wireModel="'form.email'" id="email" name="email" placeholder="Email" type="email" label="Email"
            required autofocus autocomplete="useremail" class="mt-2" />

        <x-forms.input :wireModel="'form.password'" id="password" name="password" placeholder="Password" type="password"
            label="Password" required autofocus autocomplete="current-password" class="mt-2" />

        <x-forms.checkbox :wireModel="'form.remember'" id="remember" name="remember" label="Remember me" class="mt-4"
            value="{{ $form->remember }}">
        </x-forms.checkbox>

        <div class="mt-4 flex items-center justify-end">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-buttons.button class="btn-primary ms-3" type="submit">
                {{ __('Log in') }}
            </x-buttons.button>
    </form>

</x-cards.card>
