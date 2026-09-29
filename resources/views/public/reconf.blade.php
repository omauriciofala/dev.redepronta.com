<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>RECONF - {{ $account->trade_name ?? $account->name ?? 'REDE PRONTA' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  background-color: #f8fafc;
  color: #334155;
}

#all {
  margin-top: 40px;
  margin-bottom: 50px;
}

section {
  background: #ffffff;
  border: solid #e2e8f0 1.5px;
  border-radius: 8px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.frame {
  padding: 30px 34px;
}

.text-right {
  text-align: right !important;
}

.company-name {
  color: #0f172a;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
  font-size: 1.5rem;
  line-height: 1.8rem;
  letter-spacing: -0.02em;
}

.report-name {
  color: #64748b;
  font-size: .95rem;
  line-height: 1.25rem;
  font-weight: 500;
  margin-top: 2px;
}

.b-header {
  color: #475569;
  letter-spacing: .015em;
  font-weight: 500;
  font-size: .85rem;
  line-height: 1.35rem;
}

.b-header strong {
  color: #1e293b;
}

.b-title {
  color: #64748b;
  letter-spacing: .04em;
  text-transform: uppercase;
  font-weight: 600;
  font-size: .78rem;
  line-height: 1rem;
  margin-bottom: 3px;
}

.b-data {
  color: #0f172a;
  letter-spacing: .015em;
  text-transform: uppercase;
  font-weight: 800;
  font-size: .88rem;
  line-height: 1.15rem;
}

.table-material {
  color: #1e293b;
  letter-spacing: .015em;
  font-size: .85rem;
  line-height: 1.15rem;
  margin-bottom: 0;
}

.table-material thead th {
  background-color: #f8fafc;
  color: #475569;
  font-size: .75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .05em;
  border-bottom: 2px solid #e2e8f0;
  padding: 12px 16px;
}

.table-material tbody td {
  padding: 14px 16px;
  vertical-align: middle;
  border-top: 1px solid #f1f5f9;
}

.table-material .first {
  padding-left: 34px !important;
}

.table-material .last {
  padding-right: 34px !important;
}

.table-material-title {
  color: #0f172a;
  letter-spacing: -0.01em;
  font-weight: 800;
  font-size: 1.15rem;
  line-height: 1.25rem;
  margin-bottom: 4px;
}

.table-material-description {
  color: #64748b;
  letter-spacing: .01em;
  font-weight: 500;
  font-size: 0.90rem;
  line-height: 1.15rem;
}

footer .total-items {
  color: #475569;
  font-weight: 600;
  font-size: 0.90rem;
  line-height: 1rem;
}

.type-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 700;
}
.type-ENTRY { background-color: #ecfdf5; color: #047857; }
.type-EXIT { background-color: #fef2f2; color: #b91c1c; }
.type-TRANSFER { background-color: #eff6ff; color: #1d4ed8; }
.type-RETURN { background-color: #fefce8; color: #a16207; }
.type-ADJUSTMENT { background-color: #f5f3ff; color: #6d28d9; }

@media print {
  body {
    background-color: #fff !important;
    color: #000 !important;
  }
  #all {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
  }
  section {
    border: 1px solid #ccc !important;
    box-shadow: none !important;
    page-break-inside: avoid;
  }
  .no-print {
    display: none !important;
  }
  .btn {
    display: none !important;
  }
  .frame {
    padding: 20px 24px !important;
  }
  .table-material .first {
    padding-left: 20px !important;
  }
  .table-material .last {
    padding-right: 20px !important;
  }
}
</style>
</head>
<body>

<div class="container">
  <div id="all">

    <!-- SEÇÃO 1: CABEÇALHO E METADADOS DA OPERAÇÃO -->
    <section>
      <div class="frame">
        <div class="row align-items-center mb-4">
          <div class="col-sm-8">
            <div class="d-flex align-items-center gap-3">
              <div class="p-2 rounded-3 bg-white border border-slate-200 d-inline-flex align-items-center justify-center" style="width: 58px; height: 58px;">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 42px; height: 42px;">
                  <rect width="40" height="40" rx="10" fill="#FC6714"/>
                  <path d="M12 28V12L22 17L12 22" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="28" cy="20" r="4" fill="white"/>
                </svg>
              </div>
              <div>
                <div class="company-name">{{ $account->trade_name ?? $account->name ?? 'REDE PRONTA' }}</div>
                <div class="report-name">{{ $reportTitle }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-4 text-sm-end b-header mt-3 mt-sm-0">
            <div>Protocolo: <strong>{{ $protocol }}</strong></div>
            <div>Data do Documento: <strong>{{ $documentDate }}</strong></div>
            <div>Data do Recebimento/Movimento: <strong>{{ $movementDate }}</strong></div>
          </div>
        </div>

        <hr style="border-color: #f1f5f9; margin-bottom: 20px;" />

        <div class="row mb-3 g-3">
          <div class="col-sm-3 col-6">
            <div class="b-title">DOCUMENTO</div>
            <div class="b-data">{{ $documentType }}</div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="b-title">NÚMERO</div>
            <div class="b-data">{{ $documentNumber }}</div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="b-title">TIPO</div>
            <div class="b-data">
              <span class="type-badge type-{{ $first->movement_type }}">{{ $typeLabel }}</span>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="b-title">ITENS</div>
            <div class="b-data">{{ $totalItemsCount }}</div>
          </div>
        </div>

        <div class="row mb-3 g-3">
          <div class="col-sm-3 col-6">
            <div class="b-title">POSIÇÃO REGIONAL</div>
            <div class="b-data">{{ $regionalPosition }}</div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="b-title">MOTORISTA</div>
            <div class="b-data">{{ $driverName }}</div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="b-title">RECEBEDOR</div>
            <div class="b-data">{{ $receiverName }}</div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="b-title">RESPONSÁVEL</div>
            <div class="b-data">{{ $responsibleName }}</div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-sm-6">
            <div class="b-title">SOLICITANTE / PROPRIETÁRIO</div>
            <div class="b-data">{{ $ownerName }}</div>
          </div>
          @if($first->notes)
          <div class="col-sm-6">
            <div class="b-title">OBSERVAÇÕES</div>
            <div class="b-data text-muted font-monospace" style="font-weight: normal; font-size: 0.82rem;">{{ $first->notes }}</div>
          </div>
          @endif
        </div>
      </div>
    </section>

    <!-- SEÇÃO 2: MATERIAIS INTERNALIZADOS / MOVIMENTADOS -->
    <section>
      <div class="frame pb-3">
        <div class="row">
          <div class="col-sm-12">
            <div class="table-material-title">Materiais Internalizados</div>
            <div class="table-material-description">Lista detalhada dos itens processados nesta movimentação</div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-material">
          <thead>
            <tr>
              <th scope="col" class="first" style="width: 180px;">CÓDIGO</th>
              <th scope="col">MATERIAL</th>
              <th scope="col" class="text-right" style="width: 140px;">QTD</th>
              <th scope="col" class="text-center last" style="width: 110px;">UND</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $item)
            <tr>
              <td class="first font-monospace font-semibold text-slate-700">
                <strong>{{ $item['code'] }}</strong>
              </td>
              <td>
                <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                @if(!empty($item['serials']))
                  <div class="text-primary small mt-0.5">
                    <i class="fa fa-barcode me-1"></i> {{ count($item['serials']) }} serial(is) rastreado(s)
                  </div>
                @endif
              </td>
              <td class="text-right fw-bold" style="font-size: 0.95rem;">
                {{ number_format($item['quantity'], 2, ',', '.') }}
              </td>
              <td class="text-center last font-monospace fw-bold text-secondary">
                {{ $item['unit'] }}
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">
                Nenhum item registrado para este protocolo.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <footer>
        <div class="frame pt-3 pb-3">
          <div class="row align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
              <div class="total-items">
                Total de <strong>{{ $totalItemsCount }}</strong> item(ns) listado(s)
              </div>
            </div>
            <div class="col-sm-6 text-sm-end text-start no-print">
              @if($serialsByMaterial->isNotEmpty())
              <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#serialMaterialModal">
                <i class="fa fa-barcode me-1"></i> Serializado ({{ $serialsByMaterial->sum(fn($i) => count($i['serials'])) }})
              </button>
              @endif

              <button type="button" onclick="window.print()" class="btn btn-sm btn-primary" style="background-color: #FC6714; border-color: #FC6714;">
                <i class="fa fa-print me-1"></i> Imprimir Relatório
              </button>
            </div>
          </div>
        </div>
      </footer>
    </section>

  </div>
</div>

<!-- MODAL DE SERIALIZADOS (SE HOUVER) -->
@if($serialsByMaterial->isNotEmpty())
<div class="modal fade" id="serialMaterialModal" tabindex="-1" aria-labelledby="serialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="serialModalLabel">
          <i class="fa fa-barcode text-primary me-2"></i> Equipamentos Serializados
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-material mb-0">
            <thead>
              <tr>
                <th scope="col" class="first" style="width: 45%;">MATERIAL</th>
                <th scope="col" class="last">SERIAIS / MAC</th>
              </tr>
            </thead>
            <tbody>
              @foreach($serialsByMaterial as $sItem)
              <tr>
                <td class="first align-top">
                  <div class="fw-bold">{{ $sItem['name'] }}</div>
                  <div class="text-muted font-monospace small">SKU: {{ $sItem['code'] }}</div>
                </td>
                <td class="last">
                  <div class="d-flex flex-column gap-1">
                    @foreach($sItem['serials'] as $s)
                    <div class="font-monospace small">
                      <span class="badge bg-light text-dark border">SN: {{ $s['serial_number'] }}</span>
                      @if(!empty($s['mac_address']))
                      <span class="badge bg-light text-secondary border ms-1">MAC: {{ $s['mac_address'] }}</span>
                      @endif
                    </div>
                    @endforeach
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@if($autoPrint)
<script>
  window.addEventListener('load', function() {
    window.print();
  });
</script>
@endif
</body>
</html>
