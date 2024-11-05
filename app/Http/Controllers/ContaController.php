<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContaRequest;
use App\Models\Conta;
use App\Models\SituacaoConta;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\PhpWord;

class ContaController extends Controller
{
    /**
     * Listar as contas.
     */
    public function index(Request $request)
    {
        // Recuperar os registros do banco de dados
        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
        ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
            $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
        })
        ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
            $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
        })
        ->with('situacaoConta')
        ->orderByDesc('created_at')
        ->paginate(5)
        ->withQueryString();

        // Carregar a view
        return view('contas.index', [
          'contas' => $contas,
          'nome' => $request->nome,
          'data_inicio' => $request->data_inicio,
          'data_fim' => $request->data_fim,
        ]);
    }

    /**
     * Carregar o formulário cadastrar nova conta.
     */
    public function create()
    {
        //Recuperar do banco de dados as situações
        $situacoesContas = SituacaoConta::orderBy('nome', 'asc')->get();

        // Carregar a view
        return view('contas.create', ['situacoesContas' => $situacoesContas]);
    }

    /**
     * Cadastrar no banco de dados nova conta.
     */
    public function store(ContaRequest $request)
    {
        try {
            // Validar o formulário
            $request->validated();

            // Cadastrar no banco de dados na tabela contas os valores de todos os campos
            $conta = Conta::create([
                'nome' => $request->nome,
                'valor' => str_replace(',', '.', str_replace('.', '', $request->valor)),
                'vencimento' => $request->vencimento,
                'situacao_conta_id' => $request->situacao_conta_id,
            ]);

            // Redirecionar o usuário, enviar mensagem de sucesso
            return redirect()->route('conta.show', ['conta' => $conta->id])->with('success', 'Conta cadastrada com sucesso');

        } catch (Exception $e) {

            // Salvar no log
            Log::warning('Conta não cadastrada', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar mensagem de erro
            return back()->withInput()->with('error', 'Conta não cadastrada');
        }
    }

    /**
     * Detalhes da conta.
     */
    public function show(Conta $conta)
    {

        // Carregar a view
        return view('contas.show', ['conta' => $conta]);
    }

    /**
     * Carregar o formulário editar a conta.
     */
    public function edit(Conta $conta)
    {
        //Recuperar do banco de dados as situações
        $situacoesContas = SituacaoConta::orderBy('nome', 'asc')->get();

        // Carregar a view
        return view('contas.edit', [
          'conta' => $conta,
          'situacoesContas' => $situacoesContas,
        ]);
    }

    /**
     * Editar a conta no banco de dados.
     */
    public function update(ContaRequest $request, Conta $conta)
    {
        try {
            // Validar o formulário
            $request->validated();

            // Editar as informações no banco de dados
            $conta->update([
                'nome' => $request->nome,
                'valor' => str_replace(',', '.', str_replace('.', '', $request->valor)),
                'vencimento' => $request->vencimento,
                'situacao_conta_id' => $request->situacao_conta_id,
            ]);

            // Salvar no log
            Log::info('Conta editada com sucesso', ['id' => $conta->id,'nome' => $conta->nome]);

            // Redirecionar o usuário, enviar mensagem de sucesso
            return redirect()->route('conta.show', ['conta' => $conta->id])->with('success', 'Conta editada com sucesso');

        } catch (Exception $e) {

            // Salvar no log
            Log::warning('Conta não editada', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar mensagem de erro
            return back()->withInput()->with('error', 'Conta não editada');
        }
    }

    /**
     * Excluir a conta no banco de dados.
     */
    public function destroy(Conta $conta)
    {
        // Excluir as informações no banco de dados
        $conta->delete();

        // Redirecionar o usuário, enviar mensagem de sucesso
        return redirect()->route('conta.index')->with('success', 'Conta apagada com sucesso');
    }

    public function changeSituation(Conta $conta)
    {
        try {

            //Editar as informações do registro no banco de dados
            $conta->update([
              'situacao_conta_id' => $conta->situacao_conta_id == 1 ? 2 : 1,
            ]);

            // Salvar no log
            Log::info('Situação da conta editada com sucesso', ['id' => $conta->id,'nome' => $conta->nome]);

            // Redirecionar o usuário, enviar mensagem de sucesso
            return back()->with('success', 'Situação da conta editada com sucesso');

        } catch (Exception $e) {
            // Salvar no log
            Log::warning('Situação da conta não editada', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar mensagem de erro
            return back()->with('error', 'Situação da conta não editada');
        }
    }

    // Gerar PDF

    public function gerarPdf(Request $request)
    {
        // Recuperar os registros do banco de dados
        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
      ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
          $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
      })
      ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
          $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
      })
      ->orderByDesc('created_at')
      ->get();

        // Calcular a soma total dos valores
        $totalValor = $contas->sum('valor');

        $pdf = PDF::loadView('contas.gerar-pdf', [
          'contas' => $contas,
          'totalValor' => $totalValor,
          ])->setPaper('a4', 'portrait');

        return $pdf->download('contas.pdf');

    }

    // Gerar Excel

    public function gerarCsv(Request $request)
    {
        // Recuperar os registros do banco de dados
        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
      ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
          $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
      })
      ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
          $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
      })
      ->with('situacaoConta')
      ->orderBy('vencimento')
      ->get();

        // Calcular a soma total dos valores
        $totalValor = $contas->sum('valor');

        // Criar o arquivo temporário
        $csv = tempnam(sys_get_temp_dir(), 'csv_'. Str::ulid());

        // Abrir o arquivo na forma de escrita
        $arquivoAberto = fopen($csv, 'w');

        // Criar o cabeçalho do Excel - Usar a função mb_convert_encoding para converter caracteres especiais
        $cabecalho = ['id', 'Nome', 'Vencimento', mb_convert_encoding('Situação', 'ISO-8859-1', 'UTF-8'), 'Valor'];

        // Escrever o cabeçalho no arquivo
        fputcsv($arquivoAberto, $cabecalho, ';');

        // Ler os registros recuperados do banco de dados
        foreach ($contas as $conta) {

            //Criar o array com os dados da linha do Excel
            $contaArray = [
              'id' => $conta->id,
              'nome' => mb_convert_encoding($conta->nome, 'ISO-8859-1', 'UTF-8'),
              'vencimento' => $conta->vencimento,
              'situacao' => mb_convert_encoding($conta->SituacaoConta->nome, 'ISO-8859-1', 'UTF-8'),
              'valor' => number_format($conta->valor, 2, ',', '.'),
            ];

            // Escrever o conteúdo no arquivo
            fputcsv($arquivoAberto, $contaArray, ';');
        }

        // Criar o rodapé do Excel
        $rodape = ['', '','', '','',number_format($totalValor, 2, ',', '.')];

        // Escrever o rodapé no arquivo
        fputcsv($arquivoAberto, $rodape, ';');

        // Fechar o arquivo após a escrita
        fclose($arquivoAberto);

        // Realizar o download do arquivo
        return response()->download($csv, 'relatorio_contas_' . Str::ulid() . '.csv');

    }

    public function gerarWord(Request $request)
    {
        // Recuperar os registros do banco de dados
        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
        ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
            $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
        })
        ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
            $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
        })
        ->with('situacaoConta')
        ->orderBy('vencimento')
        ->get();

        // Calcular a soma total dos valores
        $totalValor = $contas->sum('valor');

        // Criar uma instância do PHPWord
        $phpWord = new PhpWord();

        // Adicionar conteúdo ao documento
        $section = $phpWord->addSection();

        // Adicionar uma tabela
        $table = $section->addTable();

        // Definir as configurações de borda
        $borderStyle = [
          'borderColor' => '000',
          'borderSize' => 6,
        ];

        // Adicionar o cabeçalho da tabela
        $table->addRow();
        $table->addCell(2000, $borderStyle)->addText('id');
        $table->addCell(2000, $borderStyle)->addText('Nome');
        $table->addCell(2000, $borderStyle)->addText('Vencimento');
        $table->addCell(2000, $borderStyle)->addText('Situação');
        $table->addCell(2000, $borderStyle)->addText('Valor');

        // Ler os registros recuperados do banco de dados
        foreach ($contas as $conta) {

            // Adicionar a linha da tabela
            $table->addRow();
            $table->addCell(2000, $borderStyle)->addText($conta->id);
            $table->addCell(2000, $borderStyle)->addText($conta->nome);
            $table->addCell(2000, $borderStyle)->addText(Carbon::parse($conta->vencimento)->format('d/m/Y'));
            $table->addCell(2000, $borderStyle)->addText($conta->situacaoConta->nome);
            $table->addCell(2000, $borderStyle)->addText(number_format($conta->valor, 2, ',', '.'));
        }
        // Adicionar o total na tabela
        $table->addRow();
        $table->addCell(2000)->addText('');
        $table->addCell(2000)->addText('');
        $table->addCell(2000)->addText('');
        $table->addCell(2000)->addText('');
        $table->addCell(2000, $borderStyle)->addText(number_format($totalValor, 2, ',', '.'));

        // Criar o nome do arquivo
        $filename = 'relatorio_contas_' . Str::ulid() .'.docx';

        // Obter o caminho completo onde o arquivo gerado pelo PhpWord será salvo
        $savePath = storage_path($filename);

        // Salvar o arquivo
        $phpWord->save($savePath);

        // Forçar o download do arquivo no caminho indicado, após o download remover
        return response()->download($savePath)->deleteFileAfterSend(true);
    }
}
