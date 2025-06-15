<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sign Up - Beeje Coffee</title>
  <!-- <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}"> -->
  <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
</head>
<body>
  @include('components.header')
  
  <div class="login-container">
    <div class="login-card container py-5">
      <div class="row align-items-center">
        <!-- Left: Form -->
        <div class="col-md-6 px-5">
          <h2 class="fw-bold">Selamat Datang!</h2>
          <p class="text-muted mb-4">Silakan isi data diri Anda untuk mendaftar sebagai admin</p>

          @if(session('error'))
              <div class="alert alert-danger">
                  {{ session('error') }}
              </div>
          @endif

          <form method="POST" action="{{ route('admin.signup.submit') }}">
            @csrf
            
            <div class="mb-3">
              <label for="name" class="form-label mb-0 fw-bold">Nama Lengkap</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" 
                  id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
              @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label mb-0 fw-bold">Email</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" 
                  id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
              @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label mb-0 fw-bold">Password</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" 
                  id="password" name="password" placeholder="Masukkan password" required>
              @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="password_confirmation" class="form-label mb-0 fw-bold">Konfirmasi Password</label>
              <input type="password" class="form-control" 
                  id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required>
            </div>

            <button type="submit" class="btn btn-brown w-100">Daftar</button>

            <div class="text-center mt-3 mb-2">
              <p class="text-muted">atau</p>
            </div>

          
          </form>

          <div class="text-center mt-4">
            <span class="text-muted">Sudah punya akun? <a href="{{ route('admin.login') }}" class="text-decoration-none text-brown">Masuk di sini</a></span>
          </div>
        </div>

        <!-- Right: Image -->
        <div class="col-md-6 text-center position-relative">
          <div class="image-circle">
            <img src="{{ asset('images/greantea.png') }}" alt="Green Tea" class="drink-img left">
            <img src="{{ asset('images/coffe.png') }}" alt="Coffee" class="drink-img center">
            <img src="{{ asset('images/berry.png') }}" alt="Berry" class="drink-img right">
          </div>
          <h1 class="beeje-title mt-4">BEEJE COFFEE</h1>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>