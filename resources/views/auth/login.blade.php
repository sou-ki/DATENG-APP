<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label fw-bold">Email Perusahaan</label>
            <input id="email" type="email" name="email" 
                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
                   required autofocus 
                   placeholder="nama@perusahaan.com">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label fw-bold">Password</label>
            <input id="password" type="password" name="password" 
                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                   required 
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label">Ingat saya</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Login ke Sistem
            </button>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="btn btn-link">
                    Lupa password?
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>