@extends('layouts.pdf')

@section('title', 'Relatório de Ordens de Serviço')

@section('content')
<style>
    /* Paleta suave: vinho #7A2A2A | rosa claro #FAF5F5 | linha #EADFDF | cinza #999 */

    .os-section-title {
        color: #7A2A2A;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 0 0 6px 0;
        margin: 0 0 10px 0;
        border-bottom: 2px solid #E3CFCF;
    }

    /* ========== TABELA PRINCIPAL ========== */
    .rel-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 0 24px 0;
    }

    .rel-table > thead > tr > th {
        background: #FAF5F5;
        color: #7A2A2A;
        font-size: 8px;
        font-weight: bold;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 6px;
        border-bottom: 1px solid #D9BFBF;
    }

    .rel-table > tbody > tr > td {
        font-size: 8px;
        color: #333;
        padding: 8px 6px;
        border-bottom: 1px solid #F0E8E8;
        background: #fff;
    }

    /* Status */
    .status-aberta    { color: #B8863B; font-weight: 600; }
    .status-fechada   { color: #5B8F72; font-weight: 600; }
    .status-cancelada { color: #B05555; font-weight: 600; }

    /* ========== SUB-TABELA DE ITENS ========== */
    .sub-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        background: #FDFAFA;
    }

    .sub-table th {
        background: #FDFAFA;
        color: #999;
        font-size: 7px;
        font-weight: bold;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 6px 6px 4px 18px;
        border-bottom: 1px solid #EADFDF;
    }

    .sub-table td {
        font-size: 8px;
        color: #555;
        padding: 6px 6px 6px 18px;
        border-bottom: 1px solid #F3ECEC;
        background: #FDFAFA;
    }

    .sub-table td.totals-row {
        text-align: right;
        font-size: 8px;
        color: #7A2A2A;
        background: #FAF5F5;
        border-bottom: none;
        padding: 7px 6px;
    }

    .sem-itens {
        padding-left: 18px;
        color: #999;
        background: #FDFAFA;
    }

    /* ========== RESUMO DE TOTAIS ========== */
    .summary-table {
        width: 100%;
        margin: 8px 0 0 0;
        border-collapse: collapse;
    }

    .summary-table td {
        padding: 7px 8px;
        font-size: 10px;
        color: #333;
        border-bottom: 1px solid #F0E8E8;
        background: #fff;
    }

    .summary-table td.summary-header {
        font-size: 9px;
        font-weight: bold;
        color: #7A2A2A;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #FAF5F5;
        border-bottom: 1px solid #E3CFCF;
        padding: 8px;
    }

    .summary-item {
        padding-left: 20px;
    }

    .summary-value {
        text-align: right;
        white-space: nowrap;
    }

    .spacer-row {
        height: 10px;
        border-bottom: none;
    }

    .final-total {
        text-align: right;
        font-size: 11px;
        color: #7A2A2A;
        padding-top: 14px;
        border-bottom: none;
    }
</style>

<div class="container">
    <div class="os-section-title">Ordens de Serviço</div>

    <!-- Tabela principal de ordens de serviço -->
    <table class="rel-table">
        <thead>
            <tr>
                <th style="text-align: left;">#</th>
                <th style="text-align: left;">Cliente</th>
                <th style="text-align: left;">Fornecedor</th>
                <th style="text-align: left;">Pagamento</th>
                <th style="text-align: left;">Veículo</th>
                <th style="text-align: left;">Data de Emissão</th>
                <th style="text-align: left;">Autorizado Por</th>
                <th style="text-align: left;">Status</th>
                <th style="text-align: left;">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ordemServicoRelatorio as $ordem)
                <tr>
                    <td>{{ $ordem->id }}</td>
                    <td>{{ $ordem->cliente->nome ?? '' }}</td>
                    <td>{{ $ordem->fornecedor->nome ?? '' }}</td>
                    <td>{{ $ordem->formaPagamento->nome ?? '' }}</td>
                    <td>
                        {{ $ordem->veiculo->modelo ?? '' }} - 
                        {{ $ordem->veiculo->placa ?? '' }} -
                        {{ $ordem->veiculo->cor ?? '' }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($ordem->data_emissao ?? $ordem->data_abertura)->format('d/m/Y') }}</td>
                    <td>{{ $ordem->user->name ?? '' }}</td>
                    <td>
                        @if (isset($ordem->status))
                            @if ($ordem->status == 0 || $ordem->status == 'pendente' || $ordem->status == 'aberta')
                                <span class="status-aberta">Pendente</span>
                            @elseif($ordem->status == 1 || $ordem->status == 'concluida' || $ordem->status == 'fechada')
                                <span class="status-fechada">Concluído</span>
                            @else
                                <span class="status-cancelada">Cancelada</span>
                            @endif
                        @endif
                    </td>
                    <td>R$ {{ number_format($ordem->valor_total ?? $ordem->valor, 2, ',', '.') }}</td>
                </tr>
                
                <!-- Sub-tabela de itens da ordem de serviço -->
                @if (isset($ordem->itens) && count($ordem->itens))
                    <tr>
                        <td colspan="9" style="padding: 0; border-bottom: 1px solid #EADFDF;">
                            <table class="sub-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">Categoria</th>
                                        <th style="text-align: left;">Tipo</th>
                                        <th style="text-align: left;">Peça/Serviço</th>
                                        <th style="text-align: left;">Descrição</th>
                                        <th style="text-align: center;">Qtd</th>
                                        <th style="text-align: left;">V. Unitário</th>
                                        <th style="text-align: left;">V. Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalPreventiva = 0;
                                        $totalCorretiva = 0;
                                        $totalAvaria = 0;
                                        $totalMulta = 0;
                                        $totalOutros = 0;
                                    @endphp
                                    
                                    @foreach ($ordem->itens as $item)
                                        @php
                                            if ($item->tipo == 1) {
                                                $totalPreventiva += $item->valor_total;
                                            } elseif ($item->tipo == 2) {
                                                $totalCorretiva += $item->valor_total;
                                            } elseif ($item->tipo == 3) {
                                                $totalAvaria += $item->valor_total;
                                            } elseif ($item->tipo == 4) {
                                                $totalMulta += $item->valor_total;
                                            } elseif ($item->tipo == 5) {
                                                $totalOutros += $item->valor_total;
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ ($item->pecaServico->tipo ?? null) == 1 ? 'Serviço' : 'Peça' }}
                                            </td>
                                            <td>
                                                @php
                                                    $tipos = [
                                                        1 => 'Preventiva',
                                                        2 => 'Corretiva',
                                                        3 => 'Avaria',
                                                        4 => 'Multa',
                                                        5 => 'Outros',
                                                    ];
                                                @endphp
                                                {{ $tipos[$item->tipo] ?? '' }}
                                            </td>
                                            <td>{{ $item->pecaServico->nome ?? '' }}</td>
                                            <td>{{ $item->descricao }}</td>
                                            <td style="text-align: center;">{{ $item->quantidade }}</td>
                                            <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    <tr>
                                        <td colspan="7" class="totals-row">
                                            <strong>Total Preventiva:</strong> R$ {{ number_format($totalPreventiva, 2, ',', '.') }} &nbsp; | &nbsp;
                                            <strong>Total Corretiva:</strong> R$ {{ number_format($totalCorretiva, 2, ',', '.') }} &nbsp; | &nbsp;
                                            <strong>Total Avaria:</strong> R$ {{ number_format($totalAvaria, 2, ',', '.') }} &nbsp; | &nbsp;
                                            <strong>Total Multa:</strong> R$ {{ number_format($totalMulta, 2, ',', '.') }} &nbsp; | &nbsp;
                                            <strong>Total Outros:</strong> R$ {{ number_format($totalOutros, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="9" class="sem-itens">
                            <em>Sem itens</em>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Seção de totais -->
    @php
        $totaisVeiculo = [];
        $totaisCliente = [];
        $totaisFornecedor = [];
        $totaisPagamento = [];
        $totalPreventivaGeral = 0;
        $totalCorretivaGeral = 0;
        $totalAvariaGeral = 0;
        $totalMultaGeral = 0;
        $totalOutrosGeral = 0;
        
        foreach ($ordemServicoRelatorio as $ordem) {
            $valorTotal = $ordem->valor_total ?? ($ordem->valor ?? 0);
            $veiculoKey = ($ordem->veiculo->modelo ?? '') . ' ' . ($ordem->veiculo->placa ?? '');
            $clienteKey = $ordem->cliente->nome ?? '';
            $fornecedorKey = $ordem->fornecedor->nome ?? '';
            $pagamentoKey = $ordem->formaPagamento->nome ?? '';
            
            $totaisVeiculo[$veiculoKey] = ($totaisVeiculo[$veiculoKey] ?? 0) + $valorTotal;
            $totaisCliente[$clienteKey] = ($totaisCliente[$clienteKey] ?? 0) + $valorTotal;
            $totaisFornecedor[$fornecedorKey] = ($totaisFornecedor[$fornecedorKey] ?? 0) + $valorTotal;
            $totaisPagamento[$pagamentoKey] = ($totaisPagamento[$pagamentoKey] ?? 0) + $valorTotal;
            
            if (isset($ordem->itens) && count($ordem->itens)) {
                foreach ($ordem->itens as $item) {
                    if ($item->tipo == 1) {
                        $totalPreventivaGeral += $item->valor_total;
                    } elseif ($item->tipo == 2) {
                        $totalCorretivaGeral += $item->valor_total;
                    } elseif ($item->tipo == 3) {
                        $totalAvariaGeral += $item->valor_total;
                    } elseif ($item->tipo == 4) {
                        $totalMultaGeral += $item->valor_total;
                    } elseif ($item->tipo == 5) {
                        $totalOutrosGeral += $item->valor_total;
                    }
                }
            }
        }
    @endphp

    <div class="os-section-title" style="margin-top: 26px;">Resumo de Totais</div>
    <table class="summary-table">
        <!-- Totais por Veículo -->
        <tr>
            <td colspan="2" class="summary-header">Totais por Veículo:</td>
        </tr>
        @foreach ($totaisVeiculo as $veiculo => $total)
            <tr>
                <td class="summary-item">{{ $veiculo }}</td>
                <td class="summary-value">R$ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        
        <tr><td colspan="2" class="spacer-row"></td></tr>
        
        <!-- Totais por Cliente -->
        <tr>
            <td colspan="2" class="summary-header">Totais por Cliente:</td>
        </tr>
        @foreach ($totaisCliente as $cliente => $total)
            <tr>
                <td class="summary-item">{{ $cliente }}</td>
                <td class="summary-value">R$ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        
        <tr><td colspan="2" class="spacer-row"></td></tr>
        
        <!-- Totais por Fornecedor -->
        <tr>
            <td colspan="2" class="summary-header">Totais por Fornecedor:</td>
        </tr>
        @foreach ($totaisFornecedor as $fornecedor => $total)
            <tr>
                <td class="summary-item">{{ $fornecedor }}</td>
                <td class="summary-value">R$ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        
        <tr><td colspan="2" class="spacer-row"></td></tr>
        
        <!-- Totais por Pagamento -->
        <tr>
            <td colspan="2" class="summary-header">Totais por Pagamento:</td>
        </tr>
        @foreach ($totaisPagamento as $pagamento => $total)
            <tr>
                <td class="summary-item">{{ $pagamento }}</td>
                <td class="summary-value">R$ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        
        <!-- Totais Gerais por Tipo -->
        <tr>
            <td class="summary-header">Total Geral Preventiva:</td>
            <td class="summary-value">R$ {{ number_format($totalPreventivaGeral, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-header">Total Geral Corretiva:</td>
            <td class="summary-value">R$ {{ number_format($totalCorretivaGeral, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-header">Total Geral Avaria:</td>
            <td class="summary-value">R$ {{ number_format($totalAvariaGeral, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-header">Total Geral Multa:</td>
            <td class="summary-value">R$ {{ number_format($totalMultaGeral, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-header">Total Geral Outros:</td>
            <td class="summary-value">R$ {{ number_format($totalOutrosGeral, 2, ',', '.') }}</td>
        </tr>
        
        <!-- Total de Ordens -->
        <tr>
            <td colspan="2" class="final-total">
                <strong>Total de Ordens: {{ $ordemServicoRelatorio->count() }}</strong>
            </td>
        </tr>
    </table>
</div>
@endsection