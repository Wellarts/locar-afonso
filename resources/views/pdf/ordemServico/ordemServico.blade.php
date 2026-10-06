@extends('layouts.pdf')

@section('title', 'Ordem de Serviço')

@section('content')
<style>
    /* Paleta suave: vinho #7A2A2A | rosa claro #FAF5F5 | linha #EADFDF | cinza #888 */

    /* ========== FAIXA DA OS ========== */
    .os-bar {
        width: 100%;
        margin: 0 0 18px 0;
        border-top: 1px solid #EADFDF;
        border-bottom: 1px solid #EADFDF;
    }

    .os-bar td {
        text-align: center;
        padding: 9px 10px;
        font-size: 12px;
        color: #777;
        background: #fff;
        border: none;
    }

    .os-bar .os-number {
        font-size: 14px;
        font-weight: bold;
        color: #7A2A2A;
        letter-spacing: 1px;
    }

    /* ========== SEÇÕES ========== */
    .os-section-title {
        color: #7A2A2A;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 0 0 6px 0;
        margin: 0;
        border-bottom: 2px solid #E3CFCF;
    }

    .os-box {
        padding: 0;
        margin-bottom: 22px;
    }

    .os-box table { margin: 0; }

    /* ========== TABELA DE DADOS ========== */
    .data-table td,
    .data-table th {
        padding: 8px 8px;
        border-bottom: 1px solid #F0E8E8;
        font-size: 11px;
        background: #fff;
    }

    .data-table th {
        color: #999;
        font-weight: normal;
        text-align: right;
        width: 15%;
        white-space: nowrap;
        border-bottom: 1px solid #F0E8E8;
    }

    .data-table td {
        text-align: left;
        width: 18%;
        color: #333;
        font-size: 12px;
    }

    .data-table tr:last-child th,
    .data-table tr:last-child td {
        border-bottom: none;
    }

    /* ========== TABELA DE ITENS ========== */
    .items-table th {
        background: #FAF5F5;
        color: #7A2A2A;
        font-size: 10px;
        font-weight: bold;
        text-align: left;
        padding: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #D9BFBF;
    }

    .items-table td {
        font-size: 11px;
        padding: 8px;
        border-bottom: 1px solid #F0E8E8;
        vertical-align: middle;
        color: #333;
    }

    .items-table .center {
        text-align: center;
    }

    .items-table .money {
        text-align: right;
        white-space: nowrap;
    }

    /* ========== TOTAL ========== */
    .total-table {
        width: 100%;
        background: #FAF5F5;
        margin-top: 10px;
    }

    .total-table td {
        border: none;
        padding: 12px 10px;
        background: #FAF5F5;
    }

    .total-table .total-label {
        text-align: right;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        width: 70%;
    }

    .total-table .total-value {
        text-align: right;
        font-size: 16px;
        font-weight: bold;
        color: #7A2A2A;
        white-space: nowrap;
    }
</style>

<!-- Número e data da OS -->
<table class="os-bar">
    <tr>
        <td class="os-number">ORDEM DE SERVIÇO Nº OS - 00{{ $ordemServico->id }}</td>
        <td>Data de emissão: <strong>{{ \Carbon\Carbon::parse($ordemServico->data_emissao)->format('d/m/Y') }}</strong></td>
    </tr>
</table>

<!-- Dados da Ordem de Serviço -->
<div class="os-section-title">Dados da Ordem de Serviço</div>
<div class="os-box">
    <table class="data-table">
        <tr>
            <th>Cliente:</th>
            <td colspan="3">{{ $ordemServico->cliente->nome ?? '' }}</td>
            <th>Pagamento:</th>
            <td>{{ $ordemServico->formaPagamento->nome ?? '' }}</td>
        </tr>
        <tr>
            <th>Fornecedor:</th>
            <td>{{ $ordemServico->fornecedor->nome ?? '' }}</td>
            <th>Cidade:</th>
            <td>{{ $ordemServico->fornecedor->Cidade->nome ?? '' }}</td>
            <th>Estado:</th>
            <td>{{ $ordemServico->fornecedor->Estado->nome ?? '' }}</td>
        </tr>
        <tr>
            <th>Veículo:</th>
            <td>{{ $ordemServico->veiculo->modelo ?? '' }}</td>
            <th>Placa:</th>
            <td>{{ $ordemServico->veiculo->placa ?? '' }}</td>
            <th>Cor:</th>
            <td>{{ $ordemServico->veiculo->cor ?? '' }}</td>
        </tr>
        <tr>
            <th>Km Troca:</th>
            <td>{{ $ordemServico->km_troca }}</td>
            <th>Autorizado por:</th>
            <td colspan="3">{{ $ordemServico->user->name ?? '' }}</td>
        </tr>
    </table>
</div>

<!-- Itens da OS -->
<div class="os-section-title">Itens da Ordem de Serviço</div>
<div class="os-box">
    @php
        $tipos = [
            1 => 'Preventiva',
            2 => 'Corretiva',
            3 => 'Avaria',
            4 => 'Multa',
            5 => 'Outros'
        ];
    @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">Categoria</th>
                <th style="width: 12%;">Tipo</th>
                <th style="width: 19%;">Peça/Serviço</th>
                <th style="width: 24%;">Descrição</th>
                <th style="width: 6%; text-align: center;">Qtd</th>
                <th style="width: 12%; text-align: right;">V. Unitário</th>
                <th style="width: 12%; text-align: right;">V. Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ordemServico->itens as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ ($item->pecaServico->tipo ?? null) === null ? '' : ($item->pecaServico->tipo == 0 ? 'Peça' : 'Serviço') }}</td>
                    <td>{{ $tipos[$item->tipo] ?? '' }}</td>
                    <td>{{ $item->pecaServico->nome ?? '' }}</td>
                    <td>{{ $item->descricao }}</td>
                    <td class="center">{{ $item->quantidade }}</td>
                    <td class="money">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                    <td class="money">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td class="total-label">Total Geral</td>
            <td class="total-value">R$ {{ number_format($ordemServico->valor_total, 2, ',', '.') }}</td>
        </tr>
    </table>
</div>

<!-- Assinaturas (comentadas) -->
{{--
<table style="margin-top: 40px;">
    <tr>
        <td style="width: 50%; text-align: center; border: none;">
            <div style="border-bottom: 1px solid #ccc; width: 80%; margin: 0 auto 6px auto; height: 30px;"></div>
            Assinatura do Cliente
        </td>
        <td style="width: 50%; text-align: center; border: none;">
            <div style="border-bottom: 1px solid #ccc; width: 80%; margin: 0 auto 6px auto; height: 30px;"></div>
            Assinatura da Empresa
        </td>
    </tr>
</table>
--}}
@endsection