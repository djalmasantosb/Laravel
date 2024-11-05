{{-- Verificar se existe a sessão success e imprimir o valor --}}
@if (session()->has('success'))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire(
        "Pronto",
        "{{ session('success') }}",
        "success"
      );
    })
  </script>
@endif

@if ($errors->any())
  @php
    $message = '';

    foreach ($errors->all() as $error) {
        $message .= $error . '<br>';
    }
  @endphp
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire(
        "Erro",
        "{!! $message !!}",
        "error"
      );
    })
  </script>
@endif
