@extends('/layouts.admin')

@section('content')
  <div class="card border-light mb-4 mt-4 shadow">

    <div class="card-header d-flex justify-content-between">
      <span>Pesquisar</span>
    </div>

    <div class="card-body">
      <form action="{{ route('conta.index') }}">
        <div class="row">

          <div class="col-md-3 col-sm-12">
            <label class="form-label" for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ $nome }}"
              placeholder="Nome da conta">

          </div>
          <div class="col-md-3 col-sm-12">
            <label class="form-label" for="data_inicio">Data Início</label>
            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ $data_inicio }}">

          </div>
          <div class="col-md-3 col-sm-12">
            <label class="form-label" for="data_fim">Data Fim</label>
            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ $data_fim }}">
          </div>

          <div class="col-md-3 col-sm-12 mt-3 pt-4">
            <button type="submit" class="btn btn-info btn-sm">Pesquisar</button>
            <a href="{{ route('conta.index') }}" class="btn btn-warning btn-sm">Limpar</a>
          </div>

        </div>
      </form>
    </div>
  </div>

  <div class="card border-light mb-4 mt-4 shadow">
    <div class="card-header d-flex justify-content-between">
      <span>Listar as contas</span>
      <span>
        <a href="{{ route('conta.create') }}"><button type="button" class="btn btn-success btn-sm"> Cadastrar
          </button></a>
        <a href="{{ url('gerar-pdf-conta?' . request()->getQueryString()) }}"><button type="button"
            class="btn btn-warning btn-sm"> Gerar PDF
          </button></a>
        <a href="{{ url('gerar-csv-conta?' . request()->getQueryString()) }}"><button type="button"
            class="btn btn-success btn-sm"> Gerar Excel
          </button></a>
        <a href="{{ url('gerar-word-conta?' . request()->getQueryString()) }}"><button type="button"
            class="btn btn-primary btn-sm"> Gerar Word
          </button></a>
      </span>
    </div>

    {{-- Verificar se existe a sessão success e imprimir o valor --}}
    <x-alert />

    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">Valor</th>
            <th scope="col">Vencimento</th>
            <th scope="col">Situação</th>
            <th scope="col" class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($contas as $conta)
            <tr>
              <td scope="row">{{ $conta->id }}</td>
              <td>{{ $conta->nome }}</td>
              <td>{{ 'R$ ' . number_format($conta->valor, 2, ',', '.') }}</td>
              <td>
                {{ \Carbon\Carbon::parse($conta->vencimento)->tz('America/Sao_Paulo')->format('d/m/Y') }}
              </td>

              <td>
                <a href="{{ route('conta.change-situation', ['conta' => $conta->id]) }}">
                  {!! '<span class="badge text-bg-' . $conta->situacaoConta->cor . '">' . $conta->situacaoConta->nome . '</span>' !!}
                </a>
              </td>

              <td class="d-md-flex justify-content-center">
                <a href="{{ route('conta.show', ['conta' => $conta->id]) }}"><button type="button"
                    class="btn btn-primary btn-sm me-1"> Visualizar
                  </button></a>
                <a href="{{ route('conta.edit', ['conta' => $conta->id]) }}"><button
                    type="button"class="btn btn-warning btn-sm me-1"> Editar </button></a>


                <form id="formExcluir{{ $conta->id }}" action="{{ route('conta.destroy', ['conta' => $conta->id]) }}"
                  method="POST">
                  @csrf
                  @method('delete')

                  <button type="submit" class="btn btn-danger btn-sm btnDelete me-1"
                    data-delete-id="{{ $conta->id }}">
                    Apagar </button>

                </form>
              </td>
            </tr>
          @empty
            <div class="alert alert-danger" role="alert">Nenhuma conta encontrada!</div>
          @endforelse
        </tbody>
      </table>
      {{ $contas->onEachSide(0)->links() }}
    </div>
  </div>
@endsection
