<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Beeje Coffee</title>
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
          <h2 class="fw-bold">Welcome back!</h2>
          <p class="text-muted mb-4">Enter your Credentials to access your account</p>

          <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label mb-0 fw-bold">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3 password-container">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0 fw-bold">Password</label>
                <a href="#" class="forgot-password">forgot password</a>
              </div>
              <input type="password" class="form-control custom-input" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="remember" name="remember">
              <label class="form-check-label" for="remember">Remember for 30 days</label>
            </div>
            
            <button type="submit" class="btn btn-brown w-100">Login</button>

            <div class="text-center mt-3 mb-2">
              <p class="text-muted">or</p>
            </div>

            <div class="d-flex justify-content-between gap-2">
           <!-- Ubah bagian ini -->

            </div>
          </form>

          <div class="text-center mt-4">
            <span class="text-muted">Belum punya akun? <a href="{{ route('admin.signup') }}" class="text-decoration-none text-brown">Sign Up</a></span>
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
    <div class="card">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-3">
                    {{ session('error') }}
                </div>
            @endif
      </div>
  </div>

  <script type="module">
    import { signInWithGoogle } from "{{ asset('js/supabase.js') }}"
    
    document.getElementById('google-login-btn')?.addEventListener('click', (e) => {
      e.preventDefault()
      signInWithGoogle()
    })
  </script>
  
</body>
</html>
