@extends('/layouts.admin')

@section('content')
  <div class="card border-light mb-4 mt-4 shadow">
    <div class="card-header d-flex justify-content-between">
      <span>Cadastrar conta</span>
      <span>
        <a href="{{ route('conta.index') }}"><button type="button" class="btn btn-info btn-sm me-1"> Listar as contas
          </button></a>
      </span>
    </div>

    {{-- Verificar se existe a sessão success e imprimir o valor --}}
    <x-alert />

    <div class="card-body">

      <form action="{{ route('conta.store') }}" method="POST" class="row g-3">

        @csrf

        <div class="col-md-12 col-sm-12">
          <label for="nome" class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome da conta"
            value="{{ old('nome') }}">
        </div>

        <div class="col-md-4 col-sm-12">
          <label for="valor" class="form-label">Valor</label>
          <input type="text" name="valor" class="form-control" id="valor" placeholder="Valor da conta"
            value="{{ old('valor') }}">
        </div>

        <div class="col-md-4 col-sm-12">
          <label for="vencimento" class="form-label">Vencimento</label>
          <input type="date" name="vencimento" class="form-control" id="vencimento" value="{{ old('vencimento') }}">
        </div>

        <div class="col-md-4 col-sm-12">
          <label for="situacao_conta_id" class="form-label">Situação</label>
          <select name="situacao_conta_id" class="form-select select2" id="situacao_conta_id">
            <option value="">Selecione</option>
            @forelse ($situacoesContas as $situacaoConta)
              <option value="{{ $situacaoConta->id }}"
                {{ old('situacao_conta_id') == $situacaoConta->id ? 'selected' : '' }}>
                {{ $situacaoConta->nome }}
              </option>
            @empty
              <option value="">Nenhuma situação da conta encontrada</option>
            @endforelse

          </select>
        </div>

        <div>
          <div class="col-3">
            <button type="submit" class="btn btn-success btn-sm"> Cadastrar </button>
            <a href="{{ route('conta.index') }}"><button type="button" class="btn btn-danger btn-sm"> Cancelar
              </button></a>
          </div>
        </div>

    </div>

    </form>
  </div>
@endsection
