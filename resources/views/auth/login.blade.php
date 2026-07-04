<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="auth-title">Sistem Monitoring Pelatihan</h2> 
    <p class="auth-subtitle">Login ke akun MersifLab</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Alamat Email') }}</label>
            <input 
                id="email" 
                class="form-input" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                placeholder="Masukkan alamat email"
                autocomplete="username" 
            />
            @if($errors->get('email'))
                <span class="form-error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <div class="pwd-wrapper" x-data="{ show: false }">
                <input
                    x-ref="pwd"
                    id="password"
                    class="form-input"
                    type="password"
                    name="password"
                    required
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                />
                <button type="button" class="pwd-toggle"
                        x-on:click="show = !show; $refs.pwd.type = show ? 'text' : 'password'">
                    <i x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid"></i>
                </button>
            </div>
            @if($errors->get('password'))
                <span class="form-error">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <div class="flex-between">
            <label for="remember_me" class="checkbox-label">
                <input id="remember_me" type="checkbox" name="remember" checked>
                {{ __('Ingatkan Saya') }}
            </label>

            <a class="auth-link" href="https://wa.me/62895802850204?text=Halo%20Admin,%20saya%20lupa%20password%20akun%20Sistem%20Monitoring%20Pelatihan%20saya.%20Mohon%20bantuannya." target="_blank">
                {{ __('Lupa Password?') }}
            </a>
        </div>

        <button type="submit" class="btn-primary">
            {{ __('Login') }}
        </button>
        
        <p style="text-align: center; margin-top: 25px; font-size: 0.95rem; color: var(--text-muted);">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="auth-link">Register</a>
        </p>
    </form>
</x-guest-layout>
