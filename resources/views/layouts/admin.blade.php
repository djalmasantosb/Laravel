<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="icon" href="{{ asset('images/logo.ico') }}" type="image/x-icon">

  @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])

  <title>Djalma Barbosa</title>
</head>

<body>

  <header class="text-bg-primary p-3">
    <div class="container">
      <div class="d-flex align-items-center justify-content-center justify-content-lg-start flex-wrap">
        <a href="/" class="d-flex align-items-center mb-lg-0 text-decoration-none mb-2 text-white">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
            <use xlink:href="#bootstrap" />
          </svg>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto justify-content-center mb-md-0 mb-2">
          <li><a href="/" class="nav-link px-2 text-white">Home</a></li>
          <li><a href="{{ route('conta.index') }}" class="nav-link px-2 text-white">Contas</a></li>
        </ul>

        <div class="text-end">
          <button type="button" class="btn btn-warning">Login</button>
        </div>

      </div>
    </div>
  </header>

  <div class="container">

    @yield('content')

  </div>

</body>

</html>
