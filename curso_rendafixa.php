<?php
session_start();
if (file_exists(__DIR__ . '/conexao.php')) require_once __DIR__ . '/conexao.php';

function get_db_handle() {
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) return ['type'=>'mysqli','handle'=>$GLOBALS['conn']];
    if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli'] instanceof mysqli) { $GLOBALS['conn'] = $GLOBALS['mysqli']; return ['type'=>'mysqli','handle'=>$GLOBALS['conn']]; }
    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) return ['type'=>'pdo','handle'=>$GLOBALS['pdo']];
    $m = @new mysqli('127.0.0.1','root','','athenaris_db');
    if (!$m->connect_error) { $GLOBALS['conn'] = $m; return ['type'=>'mysqli','handle'=>$m]; }
    return null;
}

$db = get_db_handle();
if (!$db) die('Erro ao conectar com o banco.');

if (!isset($_SESSION['usuario_id'])) { header('Location: login.html'); exit; }
$usuario_id = (int) $_SESSION['usuario_id'];
$curso = 'renda_fixa';

$progresso = [];
if ($db['type'] === 'mysqli') {
    $conn = $db['handle'];
    $stmt = $conn->prepare("SELECT topico, concluido FROM progresso_cursos WHERE usuario_id = ? AND curso = ?");
    $stmt->bind_param('is', $usuario_id, $curso);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $progresso[(int)$r['topico']] = (int)$r['concluido'];
    $stmt->close();
} else {
    $pdo = $db['handle'];
    $q = $pdo->prepare("SELECT topico, concluido FROM progresso_cursos WHERE usuario_id = ? AND curso = ?");
    $q->execute([$usuario_id,$curso]);
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) $progresso[(int)$r['topico']] = (int)$r['concluido'];
}

$topicos = [
    ["titulo"=>"Aula 1 — O que é Renda Fixa","descricao"=>"Renda fixa engloba investimentos com retorno previsível, indexado a taxas ou indexadores.","exemplo"=>"Ex.: Tesouro Direto, CDBs e LCIs."],
    ["titulo"=>"Aula 2 — Como funciona o rendimento","descricao"=>"Rendimento pode ser prefixado, pós-fixado (ex.: % do CDI) ou atrelado à inflação (IPCA).","exemplo"=>"Ex.: 100% do CDI significa que o produto rende a mesma variação do CDI."],
    ["titulo"=>"Aula 3 — Tesouro Direto","descricao"=>"Títulos públicos para investidores pessoa física. Diversos tipos: Selic, Prefixado, IPCA+.","exemplo"=>"Ex.: Tesouro Selic ideal para reserva de emergência."],
    ["titulo"=>"Aula 4 — CDB, LCI e LCA","descricao"=>"Títulos emitidos por bancos; LCI/LCA costumam ter isenção de IR para pessoa física.","exemplo"=>"Ex.: CDB pagando 110% do CDI."],
    ["titulo"=>"Aula 5 — Debêntures","descricao"=>"Títulos de dívida emitidos por empresas. Maior risco comparado a títulos públicos; ver garantia e rating.","exemplo"=>"Ex.: debênture incentivada pode oferecer retorno atrelado ao projeto financiado."],
    ["titulo"=>"Aula 6 — Prazos e Liquidez","descricao"=>"Escolher prazo conforme objetivo: curto prazo demanda liquidez; longo prazo permite títulos mais rentáveis.","exemplo"=>"Ex.: ladder de vencimentos para equilibrar liquidez e rentabilidade."],
    ["titulo"=>"Aula 7 — Tributação e IOF","descricao"=>"IR regressivo por prazo; alíquotas menores para prazos maiores. IOF aplica-se em resgates muito rápidos.","exemplo"=>"Ex.: resgatar um CDB antes de 30 dias pode implicar IOF e menor rendimento líquido."],
    ["titulo"=>"Aula 8 — Rentabilidade real vs nominal","descricao"=>"Nominal = antes da inflação; real = descontada a inflação. Busque sempre retorno real positivo.","exemplo"=>"Ex.: se inflação 4% e rendimento 6%, ganho real ≈ 2%.","exemplo2"=>""],
    ["titulo"=>"Aula 9 — Risco de Crédito e FGC","descricao"=>"Risco que o emissor não pague. FGC protege até limite por instituição. Ver solvência do emissor.","exemplo"=>"Ex.: bancos menores podem pagar melhor, mas analisar cobertura do FGC."],
    ["titulo"=>"Aula 10 — Montando carteira conservadora","descricao"=>"Combine Tesouro, CDBs e LCI/LCA para preservar capital com rendimento superior à poupança.","exemplo"=>"Ex.: 70% Tesouro Selic + 30% CDB liquidez diária para reserva e rendimento equilibrado."]
];

?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Curso: Renda Fixa — Athenaris</title>
<link rel="stylesheet" href="css/curso.css">
</head>
<body class="corpo-dashboard">
    <aside class="sidebar">
        <div class="logo-icon"><img src="imagens/athenaris_logo.png" alt="Logo"></div>
    <nav class="nav-vertical">
        <a href="orcamento.php" class="nav-link" title="Orçamento">
            <img src="imagens/home_icone.png" alt="Início">
        </a>
        <a href="orcamento.php" class="nav-link" title="Orçamento">
            <img src="imagens/moedas_icone.webp" alt="Orçamento">
        </a>
        <a href="cursos.php" class="nav-link ativo" title="Lições">
            <img src="imagens/livro_icone.png" alt="Lições">
        </a>
        <a href="calculadora.php" class="nav-link" title="Calculadora">
            <img src="imagens/calculadora_icone.png" alt="Calculadora">
        </a>
        <a href="investimentos.php" class="nav-link" title="Investimentos">
            <img src="imagens/acoes_icone.png" alt="Investimentos">
        </a>
    </nav>
    </aside>

    <main class="main-content">
        <h1 class="titulo-curso">Curso: Renda Fixa</h1>
        <h2 class="subtitulo-curso">Princípios e produtos para investir com segurança</h2>
        <div id="barra-progresso"><div id="progresso-preenchido"></div></div>

        <?php foreach ($topicos as $idx => $t):
            $num = $idx + 1;
            $checked = (!empty($progresso[$num]) && $progresso[$num]) ? 'checked' : '';
        ?>
            <div class="bloco-topico" data-topico="<?= $num ?>">
                <div class="topico-header">
                    <h3><?= $num ?>. <?= htmlspecialchars($t['titulo']) ?></h3>
                    <input type="checkbox" class="check-progresso" <?= $checked ?>>
                </div>
                <p><?= nl2br(htmlspecialchars($t['descricao'])) ?></p>
                <div class="topico-exemplo"><strong>Exemplo:</strong> <?= nl2br(htmlspecialchars($t['exemplo'])) ?></div>
            </div>
        <?php endforeach; ?>
    </main>

    <script src="js/curso.js"></script>
    <script>inicializarCurso('<?= $curso ?>', <?= json_encode($progresso) ?>);</script>
</body>
</html>
