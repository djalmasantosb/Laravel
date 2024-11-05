@extends('/layouts.admin')

@section('content')
  <div class="card border-light mb-4 mt-4 shadow">
    <div class="card-header d-flex justify-content-between">
      <span>Visualizar</span>
      <span>
        <a href="{{ route('conta.index') }}"><button type="button" class="btn btn-info btn-sm me-1"> Listar as contas
          </button></a>

        <a href="{{ route('conta.edit', ['conta' => $conta->id]) }}"><button type="button"
            class="btn btn-warning btn-sm me-1"> Editar </button></a>

        <a href="{{ route('conta.create') }}"><button type="button" class="btn btn-success btn-sm"> Cadastrar </button></a>
      </span>
    </div>

    {{-- Verificar se existe a sessão success e imprimir o valor --}}
    <x-alert />

    <div class="card-body">

      <dl class="row">
        <dt class="col-sm-3">ID</dt>
        <dd class="col-sm-9">{{ $conta->id }}</dd>

        <dt class="col-sm-3">Nome</dt>
        <dd class="col-sm-9">{{ $conta->nome }}</dd>

        <dt class="col-sm-3">Valor</dt>
        <dd class="col-sm-9">{{ 'R$ ' . number_format($conta->valor, 2, ',', '.') }}</dd>

        <dt class="col-sm-3">Vencimento</dt>
        <dd class="col-sm-9">
          {{ \Carbon\Carbon::parse($conta->vencimento)->tz('America/Sao_Paulo')->format('d/m/Y') }}
        </dd>

        <dt class="col-sm-3">Situação</dt>
        <dd class="col-sm-9">
          <a href="{{ route('conta.change-situation', ['conta' => $conta->id]) }}">
            {!! '<span class="badge text-bg-' . $conta->situacaoConta->cor . '">' . $conta->situacaoConta->nome . '</span>' !!}
          </a>
        </dd>
        <br>
        <br>
        <dt class="col-sm-3">Criado</dt>
        <dd class="col-sm-9">
          {{ \Carbon\Carbon::parse($conta->created_at)->tz('America/Sao_Paulo')->format('d/m/Y H:i:s') }}
        </dd>

        <dt class="col-sm-3">Editado</dt>
        <dd class="col-sm-9">
          {{ \Carbon\Carbon::parse($conta->update_at)->tz('America/Sao_Paulo')->format('d/m/Y H:i:s') }}
        </dd>
      </dl>

    </div>
  @endsection
