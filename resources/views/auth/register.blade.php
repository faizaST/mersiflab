<x-guest-layout>
    <h2 class="auth-title">Sistem Monitoring Pelatihan</h2>
    <p class="auth-subtitle">Buat Akun Sekarang</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">{{ __('Nama Lengkap') }}</label>
            <input 
                id="name" 
                class="form-input" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                placeholder="Faiza Shalikashilla"
                autocomplete="name" 
            />
            @if($errors->get('name'))
                <span class="form-error">{{ $errors->first('name') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Alamat Email') }}</label>
            <input 
                id="email" 
                class="form-input" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                placeholder="name@example.com"
                autocomplete="username" 
            />
            @if($errors->get('email'))
                <span class="form-error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="phone">{{ __('Nomor Telepon') }}</label>
            <input 
                id="phone" 
                class="form-input" 
                type="text" 
                name="phone" 
                value="{{ old('phone') }}" 
                required 
                placeholder="08123456789"
                autocomplete="tel" 
            />
            @if($errors->get('phone'))
                <span class="form-error">{{ $errors->first('phone') }}</span>
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
                    placeholder="Buat password yang kuat"
                    autocomplete="new-password"
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

        <div class="form-group">
            <label class="form-label" for="password_confirmation">{{ __('Konfirmasi Password') }}</label>
            <div class="pwd-wrapper" x-data="{ show: false }">
                <input
                    x-ref="pwdConfirm"
                    id="password_confirmation"
                    class="form-input"
                    type="password"
                    name="password_confirmation"
                    required
                    placeholder="Ulangi password"
                    autocomplete="new-password"
                />
                <button type="button" class="pwd-toggle"
                        x-on:click="show = !show; $refs.pwdConfirm.type = show ? 'text' : 'password'">
                    <i x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid"></i>
                </button>
            </div>
            @if($errors->get('password_confirmation'))
                <span class="form-error">{{ $errors->first('password_confirmation') }}</span>
            @endif
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 10px;">
            {{ __('Daftar Akun') }}
        </button>
        
        <p style="text-align: center; margin-top: 25px; font-size: 0.95rem; color: var(--text-muted);">
            Sudah Memiliki Akun? 
            <a href="{{ route('login') }}" class="auth-link">Login</a>
        </p>
    </form>
</x-guest-layout>
