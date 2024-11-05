@extends('/layouts.admin')

@section('content')
  <div class="card border-light mb-4 mt-4 shadow">
    <div class="card-header">
      <h4>Projeto Laravel 10</h4>
    </div>

    <div class="card-header">
      <a href="{{ route('conta.index') }}"><button type="button" class="btn btn-info btn-sm me-1"> Listar as contas
        </button></a>
    </div>
  @endsection
