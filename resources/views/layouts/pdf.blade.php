<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Relatório PDF')</title>

    <style>
        /* Paleta suave: vinho #7A2A2A | rosa claro #FAF5F5 | linha #EADFDF | cinza #888 */

        @page {
            margin: 1.5cm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #ffffff;
            color: #333;
            margin: 0 auto;
            padding: 0;
            width: 100%;
            max-width: 21cm;
        }

        .report-container {
            width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 0;
        }

        /* ========== CABEÇALHO (apenas tabela; DomPDF não suporta display:flex) ========== */
        .pdf-header-table {
            width: 100%;
            margin: 0 0 18px 0;
            border-collapse: collapse;
        }

        .pdf-header-cell {
            background: #FAF5F5;
            color: #333;
            text-align: center;
            padding: 16px 20px;
            border-bottom: 2px solid #C9A3A3;
        }

        .pdf-logo {
            height: 56px;
            width: auto;
            max-width: 160px;
            margin-bottom: 6px;
        }

        .pdf-company {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
            color: #7A2A2A;
        }

        .pdf-title {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #999;
            margin: 3px 0 8px 0;
        }

        .pdf-meta {
            font-size: 10px;
            line-height: 1.6;
            color: #777;
            margin: 0;
        }

        /* ========== CONTEÚDO ========== */
        .main-content {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        .section-title {
            text-align: center;
            color: #7A2A2A;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .badge {
            display: inline-block;
            background: #F1E6E6;
            color: #7A2A2A;
            padding: 5px 12px;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .info-line {
            font-size: 0.95rem;
            margin: 18px 0;
            padding: 12px 16px;
            background: #FAF5F5;
            border: 1px solid #EADFDF;
            border-radius: 8px;
        }

        .info-item strong {
            color: #7A2A2A;
            font-weight: 600;
            margin-right: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 0.95rem;
        }

        th,
        td {
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background: #FAF5F5;
            font-weight: 600;
            color: #7A2A2A;
            border-bottom: 1px solid #D9BFBF;
        }

        td {
            background: #fff;
            border-bottom: 1px solid #F0E8E8;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            font-size: 1rem;
            padding: 15px;
            background: transparent;
            border: 1px solid #EADFDF;
            border-radius: 6px;
            page-break-inside: avoid;
        }

        .summary-row {
            margin-bottom: 8px;
        }

        .summary-row strong {
            color: #7A2A2A;
            font-weight: 600;
        }

        .signature {
            text-align: center;
            margin-top: 36px;
        }

        .signature hr {
            width: 60%;
            margin: 20px auto 10px;
            border: 0;
            border-top: 1px solid #ccc;
        }

        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #EADFDF;
            font-size: 0.8rem;
            color: #999;
            width: 100%;
        }
    </style>
</head>

<body>
    @php
        $p = null;
        try {
            $p = \Illuminate\Support\Facades\DB::table('parametros')->first();
        } catch (\Throwable $e) {
            $p = null;
        }
        $companyName    = $p->empresa_nome ?? ($p->nome_empresa ?? ($p->nome ?? 'Nome da Empresa'));
        $companyCnpj    = $p->cpf_cnpj ?? '00.000.000/0000-00';
        $companyAddress = $p->endereco_completo ?? 'Sem Endereço Informado';
        $companyPhones  = $p->telefone ?? '(00) 0000-0000';
        $companyInsta   = $p->redes_sociais ?? '@empresa';
        $logo           = $p->logo ?? null;
        $logoPath       = $logo ? public_path('storage/' . $logo) : null;
        $logoExiste     = $logoPath && file_exists($logoPath);
    @endphp

    <div class="report-container">
        <table class="pdf-header-table">
            <tr>
                <td class="pdf-header-cell">
                    @if ($logoExiste)
                        <img class="pdf-logo" src="{{ $logoPath }}" alt="Logo da Empresa"><br>
                    @endif
                    <div class="pdf-company">{{ $companyName }}</div>
                    <div class="pdf-title">@yield('title', 'Relatório')</div>
                    <div class="pdf-meta">
                        CNPJ/CPF: {{ $companyCnpj }} &nbsp;|&nbsp; Telefones: {{ $companyPhones }}<br>
                        {{ $companyAddress }}<br>
                        {{ $companyInsta }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="main-content">
            @yield('content')
            @yield('summary')
        </div>

        <div class="footer">
            Documento gerado em {{ date('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>