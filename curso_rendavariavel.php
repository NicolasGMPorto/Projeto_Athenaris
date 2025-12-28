<?php
session_start();

// garantir conexão (mysqli ou PDO) com fallback
if (file_exists(__DIR__ . '/conexao.php')) require_once __DIR__ . '/conexao.php';

// helper para detectar conexão disponível ou criar fallback
function get_db_handle() {
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) return ['type'=>'mysqli','handle'=>$GLOBALS['conn']];
    if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli'] instanceof mysqli) { $GLOBALS['conn'] = $GLOBALS['mysqli']; return ['type'=>'mysqli','handle'=>$GLOBALS['conn']]; }
    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) return ['type'=>'pdo','handle'=>$GLOBALS['pdo']];

    // fallback Laravel/XAMPP/Laragon padrão
    $m = @new mysqli('127.0.0.1','root','','athenaris_db');
    if (!$m->connect_error) { $GLOBALS['conn'] = $m; return ['type'=>'mysqli','handle'=>$m]; }
    return null;
}

$db = get_db_handle();
if (!$db) {
    die('Erro: não foi possível estabelecer conexão com o banco. Verifique conexao.php');
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}
$usuario_id = (int) $_SESSION['usuario_id'];
$curso = 'renda_variavel';

// buscar progresso conforme driver
$progresso = [];
if ($db['type'] === 'mysqli') {
    /** @var mysqli $conn */
    $conn = $db['handle'];
    $stmt = $conn->prepare("SELECT topico, concluido FROM progresso_cursos WHERE usuario_id = ? AND curso = ?");
    $stmt->bind_param('is', $usuario_id, $curso);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $progresso[(int)$r['topico']] = (int)$r['concluido'];
    $stmt->close();
} else {
    /** @var PDO $pdo */
    $pdo = $db['handle'];
    $q = $pdo->prepare("SELECT topico, concluido FROM progresso_cursos WHERE usuario_id = ? AND curso = ?");
    $q->execute([$usuario_id,$curso]);
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) $progresso[(int)$r['topico']] = (int)$r['concluido'];
}

// tópicos (didáticos) — 10 itens
$topicos = [
    ["titulo"=>"Aula 1 — Introdução à Renda Variável","descricao"=>"Renda variável engloba ativos cujo retorno não é previsível no curto prazo — ex.: ações, ETFs e FIIs. Ideal para objetivos de longo prazo devido à sua volatilidade.","exemplo"=>"Ex.: comprar ações de uma empresa e manter por anos, aproveitando crescimento e dividendos."],
    ["titulo"=>"Aula 2 — Ações: Propriedade e Dividendos","descricao"=>"Ação representa fração de uma empresa. Alguns papéis pagam dividendos—parte do lucro distribuído aos acionistas.","exemplo"=>"Ex.: possuir 100 ações de uma empresa que paga R$1 por ação em dividendos = R$100 recebido."],
    ["titulo"=>"Aula 3 — Risco x Retorno em Ações","descricao"=>"Ativos de maior crescimento tendem a ser mais arriscados. Entender horizonte e tolerância a perdas é essencial.","exemplo"=>"Ex.: ação de tecnologia tem alta volatilidade; utility tende a ser mais estável."],
    ["titulo"=>"Aula 4 — ETFs e Fundos de Ações","descricao"=>"ETFs replicam índices e permitem diversificação. Fundos de ações reúnem gestões ativas; atenção a taxas.","exemplo"=>"Ex.: comprar ETF do S&P500 para diversificar exposição internacional."],
    ["titulo"=>"Aula 5 — Fundos Imobiliários (FIIs)","descricao"=>"FIIs investem em imóveis ou títulos imobiliários e distribuem rendimentos periódicos; atenção à vacância e gestão.","exemplo"=>"Ex.: receber aluguéis proporcionais através de cotas de um FII."],
    ["titulo"=>"Aula 6 — Dividendos e JCP","descricao"=>"Dividendos são distribuição de lucro; JCP (juros sobre capital próprio) também remunera acionistas, com tributação diferente.","exemplo"=>"Ex.: empresa sólida que paga 4% ao ano em dividendos sobre o preço da ação."],
    ["titulo"=>"Aula 7 — Análise Fundamentalista","descricao"=>"Avalia saúde financeira da empresa: receitas, lucros, endividamento e fluxo de caixa para encontrar valor real.","exemplo"=>"Ex.: usar P/L (preço/lucro) para comparar empresas do mesmo setor."],
    ["titulo"=>"Aula 8 — Análise Técnica","descricao"=>"Baseada em gráficos e padrões de preço; útil para operações de curto prazo mas não substitui estudo fundamentalista.","exemplo"=>"Ex.: identificar suporte e resistência para planejar entradas e stops."],
    ["titulo"=>"Aula 9 — Estratégias (Buy & Hold, Trade)","descricao"=>"Buy & hold visa longo prazo; trading busca curto prazo. Escolha estratégia conforme perfil e tempo disponível.","exemplo"=>"Ex.: alocar 70% em buy & hold e 30% para estudos/treinos em trade (simulado)."],
    ["titulo"=>"Aula 10 — Montando carteira de longo prazo","descricao"=>"Diversifique por setor, geografia e classe; reavalie periodicamente e rebalence conforme objetivos.","exemplo"=>"Ex.: carteira: 50% ações, 20% ETFs internacionais, 20% FIIs, 10% caixa para oportunidades."]
];

?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Curso: Renda Variável — Athenaris</title>
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
        <h1 class="titulo-curso">Curso: Renda Variável</h1>
        <h2 class="subtitulo-curso">Aprofunde-se no universo de ações, ETFs e estratégias para crescimento</h2>

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
