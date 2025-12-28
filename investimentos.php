<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html'); 
    exit;
}

require_once 'conexao.php'; 

$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';

// Dados simulados dos rankings
$acoes_dividend_yield = [
    ['codigo' => 'MOAR3', 'nome' => 'MONTEIRO ARARUNA', 'valor' => '66,93%'],
    ['codigo' => 'MELK3', 'nome' => 'MELNICK', 'valor' => '30,90%'],
    ['codigo' => 'ALLD3', 'nome' => 'ALLIED', 'valor' => '23,71%'],
    ['codigo' => 'RECV3', 'nome' => 'PETRORECÔNCAVO', 'valor' => '17,86%'],
    ['codigo' => 'PETR4', 'nome' => 'PETROBRÁS', 'valor' => '17,52%']
];

$acoes_valor_mercado = [
    ['codigo' => 'PETR4', 'nome' => 'PETROBRÁS', 'valor' => 'R$ 398,65 B'],
    ['codigo' => 'ITUB4', 'nome' => 'BANCO ITAÚ', 'valor' => 'R$ 387,93 B'],
    ['codigo' => 'VALE3', 'nome' => 'VALE', 'valor' => 'R$ 281,46 B'],
    ['codigo' => 'BPAC11', 'nome' => 'BANCO BTG PACTUAL', 'valor' => 'R$ 239,86 B'],
    ['codigo' => 'ABEV3', 'nome' => 'AMBEV', 'valor' => 'R$ 190,87 B']
];

$fiis_valor_mercado = [
    ['codigo' => 'HGLG11', 'nome' => 'CSHG LOGÍSTICA', 'valor' => 'R$ 493,12 B'],
    ['codigo' => 'XPML11', 'nome' => 'XP MALLS', 'valor' => 'R$ 369,42 B'],
    ['codigo' => 'KNRI11', 'nome' => 'KINEA RENDA IMOBILIÁRIA', 'valor' => 'R$ 289,51 B'],
    ['codigo' => 'HGRU11', 'nome' => 'CSHG URBANA', 'valor' => 'R$ 251,73 B'],
    ['codigo' => 'VISC11', 'nome' => 'VINCI SHOPPING CENTERS', 'valor' => 'R$ 234,41 B']
];

$fiis_dividend_yield = [
    ['codigo' => 'RBRP11', 'nome' => 'RBR PROPERTIES', 'valor' => '12,45%'],
    ['codigo' => 'HCTR11', 'nome' => 'HECTARE CE', 'valor' => '11,82%'],
    ['codigo' => 'BCFF11', 'nome' => 'BTG PACTUAL FUNDO DE FUNDOS', 'valor' => '10,93%'],
    ['codigo' => 'VINO11', 'nome' => 'VINCI OFFICES', 'valor' => '9,87%'],
    ['codigo' => 'MXRF11', 'nome' => 'MAXI RENDA', 'valor' => '9,45%']
];

// Verificar se foi solicitado detalhes de um ativo específico
$ativo_detalhado = null;
if (isset($_GET['ativo'])) {
    $ativo_codigo = $_GET['ativo'];
    // Buscar dados do ativo (simulação)
    $todos_ativos = array_merge($acoes_dividend_yield, $acoes_valor_mercado, $fiis_valor_mercado, $fiis_dividend_yield);
    foreach ($todos_ativos as $ativo) {
        if ($ativo['codigo'] === $ativo_codigo) {
            $ativo_detalhado = $ativo;
            break;
        }
    }
    
    // Dados simulados para o gráfico e detalhes
    if ($ativo_detalhado) {
        $ativo_detalhado['valor_atual'] = 'R$ ' . rand(10, 200) . ',' . rand(10, 99);
        $ativo_detalhado['dividendos'] = 'R$ ' . rand(1, 20) . ',' . rand(10, 99);
        $ativo_detalhado['variacao_dia'] = (rand(0, 1) ? '+' : '-') . rand(0, 5) . ',' . rand(10, 99) . '%';
        $ativo_detalhado['variacao_mes'] = (rand(0, 1) ? '+' : '-') . rand(0, 15) . ',' . rand(10, 99) . '%';
        $ativo_detalhado['variacao_ano'] = (rand(0, 1) ? '+' : '-') . rand(0, 50) . ',' . rand(10, 99) . '%';
        
        // Dados simulados para o gráfico (últimos 12 meses)
        $ativo_detalhado['historico'] = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = date('M', strtotime("-$i months"));
            $valor = rand(50, 200);
            $ativo_detalhado['historico'][] = ['mes' => $mes, 'valor' => $valor];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investimentos - Athenaris</title>
    <link rel="stylesheet" href="CSS/home.css">
    <link rel="stylesheet" href="CSS/investimentos.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Merriweather+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="corpo-dashboard">

    <aside class="sidebar">
        <div class="logo-icon" title="Athenaris">
            <img src="imagens/athenaris_logo.png" alt="Logo Athenaris" style="width: 50px; height: 50px;">
        </div>
        <nav class="nav-vertical">
            <a href="home.php" class="nav-link" title="Início">
                <img src="imagens/home_icone.png" alt="Início">
            </a>
            <a href="orcamento.php" class="nav-link" title="Orçamento">
                <img src="imagens/moedas_icone.webp" alt="Orçamento">
            </a>
            </a>
            <a href="cursos.php" class="nav-link" title="Lições">
                <img src="imagens/livro_icone.png" alt="Lições">
            </a>
            <a href="calculadora.php" class="nav-link" title="Calculadora">
                <img src="imagens/calculadora_icone.png" alt="Calculadora">
            </a>
            <a href="investimentos.php" class="nav-link ativo" title="Investimentos">
                <img src="imagens/acoes_icone.png" alt="Investimentos">
            </a>
        </nav>
    </aside>

    <header class="header">
        <div class="user-info">
            <span>Bem-vindo, <?php echo strtoupper(htmlspecialchars($nome_usuario)); ?>!</span>
            <a href="perfil.php" class="perfil-link" title="Acessar Perfil"> 
                <img src="<?php echo htmlspecialchars($foto_usuario); ?>" alt="Foto de perfil" class="perfil-foto">
            </a> 
        </div>
    </header>

   <center> <main class="main-content">
        <div class="investimentos-container">
            <h1 class="titulo-principal">Rankings de Ativos</h1>
            <p class="subtitulo">Acompanhe os melhores investimentos do mercado</p>
            
            <?php if (!$ativo_detalhado): ?>
                <!-- Tela principal com rankings -->
                <div class="categorias-tabs">
                    <div class="categoria-tab ativo" data-categoria="acoes">Ações</div>
                    <div class="categoria-tab" data-categoria="fiis">Fundos Imobiliários</div>
                </div>
                
                <div class="categoria-conteudo ativo" id="conteudo-acoes">
                    <div class="rankings-grid">
                        <div class="ranking-card">
                            <h3 class="ranking-titulo">Maiores Dividend Yield</h3>
                            <?php foreach ($acoes_dividend_yield as $index => $acao): ?>
                                <div class="ranking-item" onclick="verDetalhes('<?php echo $acao['codigo']; ?>')">
                                    <div class="ranking-posicao">#<?php echo $index + 1; ?></div>
                                    <div class="ranking-info">
                                        <div class="ranking-codigo"><?php echo $acao['codigo']; ?></div>
                                        <div class="ranking-nome"><?php echo $acao['nome']; ?></div>
                                    </div>
                                    <div class="ranking-valor"><?php echo $acao['valor']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div> 
                        
                        <div class="ranking-card">
                            <h3 class="ranking-titulo">Maiores Valor de Mercado</h3>
                            <?php foreach ($acoes_valor_mercado as $index => $acao): ?>
                                <div class="ranking-item" onclick="verDetalhes('<?php echo $acao['codigo']; ?>')">
                                    <div class="ranking-posicao">#<?php echo $index + 1; ?></div>
                                    <div class="ranking-info">
                                        <div class="ranking-codigo"><?php echo $acao['codigo']; ?></div>
                                        <div class="ranking-nome"><?php echo $acao['nome']; ?></div>
                                    </div>
                                    <div class="ranking-valor"><?php echo $acao['valor']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="categoria-conteudo" id="conteudo-fiis">
                    <div class="rankings-grid">
                        <div class="ranking-card">
                            <h3 class="ranking-titulo">Maiores Dividend Yield</h3>
                            <?php foreach ($fiis_dividend_yield as $index => $fii): ?>
                                <div class="ranking-item" onclick="verDetalhes('<?php echo $fii['codigo']; ?>')">
                                    <div class="ranking-posicao">#<?php echo $index + 1; ?></div>
                                    <div class="ranking-info">
                                        <div class="ranking-codigo"><?php echo $fii['codigo']; ?></div>
                                        <div class="ranking-nome"><?php echo $fii['nome']; ?></div>
                                    </div>
                                    <div class="ranking-valor"><?php echo $fii['valor']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="ranking-card">
                            <h3 class="ranking-titulo">Maiores Valor de Mercado</h3>
                            <?php foreach ($fiis_valor_mercado as $index => $fii): ?>
                                <div class="ranking-item" onclick="verDetalhes('<?php echo $fii['codigo']; ?>')">
                                    <div class="ranking-posicao">#<?php echo $index + 1; ?></div>
                                    <div class="ranking-info">
                                        <div class="ranking-codigo"><?php echo $fii['codigo']; ?></div>
                                        <div class="ranking-nome"><?php echo $fii['nome']; ?></div>
                                    </div>
                                    <div class="ranking-valor"><?php echo $fii['valor']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Tela de detalhes do ativo -->
                <div class="detalhes-container">
                    <div class="detalhes-header">
                        <h2 class="detalhes-titulo"><?php echo $ativo_detalhado['codigo'] . ' - ' . $ativo_detalhado['nome']; ?></h2>
                        <button class="btn-voltar" onclick="voltarParaRankings()">← Voltar aos Rankings</button>
                    </div>
                    
                    <div class="detalhes-info">
                        <div class="info-card">
                            <div class="info-label">Valor Atual</div>
                            <div class="info-valor"><?php echo $ativo_detalhado['valor_atual']; ?></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Dividendos Anuais</div>
                            <div class="info-valor"><?php echo $ativo_detalhado['dividendos']; ?></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Variação (Dia)</div>
                            <div class="info-valor <?php echo strpos($ativo_detalhado['variacao_dia'], '+') !== false ? 'positivo' : 'negativo'; ?>">
                                <?php echo $ativo_detalhado['variacao_dia']; ?>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Variação (Mês)</div>
                            <div class="info-valor <?php echo strpos($ativo_detalhado['variacao_mes'], '+') !== false ? 'positivo' : 'negativo'; ?>">
                                <?php echo $ativo_detalhado['variacao_mes']; ?>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Variação (Ano)</div>
                            <div class="info-valor <?php echo strpos($ativo_detalhado['variacao_ano'], '+') !== false ? 'positivo' : 'negativo'; ?>">
                                <?php echo $ativo_detalhado['variacao_ano']; ?>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Desempenho (Últimos 12 Meses)</h3>
                    <div class="grafico-container">
                        <canvas id="graficoAtivo"></canvas>
                    </div>
                </div>
                
                <script>
                    // Gerar gráfico com dados simulados
                    const ctx = document.getElementById('graficoAtivo').getContext('2d');
                    const meses = <?php echo json_encode(array_column($ativo_detalhado['historico'], 'mes')); ?>;
                    const valores = <?php echo json_encode(array_column($ativo_detalhado['historico'], 'valor')); ?>;
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: meses,
                            datasets: [{
                                label: 'Valor (R$)',
                                data: valores,
                                borderColor: '#00A36C',
                                backgroundColor: 'rgba(0, 163, 108, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php endif; ?>
        </div>
    </main>
</center>
    <footer class="footer">
        | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos  
    </footer>

    <script>
        // Função para ver detalhes de um ativo
        function verDetalhes(codigoAtivo) {
            window.location.href = 'investimentos.php?ativo=' + codigoAtivo;
        }
        
        // Função para voltar aos rankings
        function voltarParaRankings() {
            window.location.href = 'investimentos.php';
        }
        
        // Sistema de abas para categorias
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.categoria-tab');
            const conteudos = document.querySelectorAll('.categoria-conteudo');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const categoria = this.getAttribute('data-categoria');
                    
                    // Atualizar tabs
                    tabs.forEach(t => t.classList.remove('ativo'));
                    this.classList.add('ativo');
                    
                    // Atualizar conteúdos
                    conteudos.forEach(c => c.classList.remove('ativo'));
                    document.getElementById('conteudo-' + categoria).classList.add('ativo');
                });
            });
        });
    </script>
</body>
</html>