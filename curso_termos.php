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
$curso = 'termos_investimentos';

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

// termos importantes
$termos = [
    ["termo" => "Educação Financeira", "definicao" => "Entenda a importância de aprender a gerenciar seu dinheiro e tomar decisões conscientes."],
    ["termo" => "Investimento", "definicao" => "Aplicar dinheiro em algo esperando retorno futuro. Exemplo: comprar ações ou investir em CDB."],
    ["termo" => "Rentabilidade", "definicao" => "A taxa de retorno de um investimento. Exemplo: 10% ao ano."],
    ["termo" => "Risco", "definicao" => "A chance de perder parte ou todo o dinheiro investido. Maior risco → potencial de maior retorno."],
    ["termo" => "Liquidez", "definicao" => "Facilidade de converter o investimento em dinheiro sem perda de valor."],
    ["termo" => "Inflação", "definicao" => "Aumento geral dos preços com o tempo. Corrói o poder de compra do dinheiro."],
    ["termo" => "Juros Compostos", "definicao" => "Juros sobre juros. É o principal motor do crescimento de investimentos a longo prazo."],
    ["termo" => "Diversificação", "definicao" => "Distribuir seu dinheiro entre diferentes tipos de investimentos para reduzir riscos."],
    ["termo" => "Orçamento Pessoal", "definicao" => "Controle das receitas e despesas para planejar o uso do dinheiro."],
    ["termo" => "Patrimônio", "definicao" => "Conjunto de bens, direitos e investimentos de uma pessoa."]
];

?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Termos Importantes - Athenaris</title>
<link rel="stylesheet" href="css/curso.css">
<style>
    .bloco-termo {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 800px;
        margin-bottom: 20px;
        padding: 20px 25px;
        position: relative;
    }
    
    .termo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .termo-titulo {
        color: var(--cor-primaria);
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
    }
    
    .termo-definicao {
        color: var(--cor-texto-escuro);
        line-height: 1.6;
        margin: 0;
    }
    
    .check-progresso {
        transform: scale(1.3);
        accent-color: var(--cor-destaque-sucesso);
        cursor: pointer;
    }
</style>
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
        <h1 class="titulo-curso">Termos Importantes dos Investimentos</h1>
        <h2 class="subtitulo-curso">Conceitos fundamentais que todo investidor precisa conhecer</h2>

        <div id="barra-progresso">
            <div id="progresso-preenchido"></div>
        </div>

        <?php foreach ($termos as $idx => $t): 
            $num = $idx + 1;
            $checked = (!empty($progresso[$num]) && $progresso[$num]) ? 'checked' : '';
        ?>
            <div class="bloco-termo" data-topico="<?= $num ?>">
                <div class="termo-header">
                    <h3 class="termo-titulo"><?= htmlspecialchars($t['termo']) ?></h3>
                    <input type="checkbox" class="check-progresso" <?= $checked ?>>
                </div>
                <p class="termo-definicao"><?= nl2br(htmlspecialchars($t['definicao'])) ?></p>
            </div>
        <?php endforeach; ?>
    </main>

    <script>
        // JavaScript para controlar o progresso
        function inicializarTermos(curso, progresso) {
            const checkboxes = document.querySelectorAll('.check-progresso');
            const barraProgresso = document.getElementById('progresso-preenchido');
            
            // Atualizar barra de progresso inicial
            atualizarBarraProgresso();
            
            // Adicionar eventos aos checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const topico = this.closest('.bloco-termo').dataset.topico;
                    const concluido = this.checked ? 1 : 0;
                    
                    // Salvar no banco de dados via AJAX
                    salvarProgresso(curso, topico, concluido);
                    
                    // Atualizar barra de progresso
                    atualizarBarraProgresso();
                });
            });
            
            function atualizarBarraProgresso() {
                const total = checkboxes.length;
                const concluidos = document.querySelectorAll('.check-progresso:checked').length;
                const porcentagem = (concluidos / total) * 100;
                
                barraProgresso.style.width = porcentagem + '%';
            }
            
            function salvarProgresso(curso, topico, concluido) {
                // Enviar requisição AJAX para salvar o progresso
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'salvar_progresso.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(`curso=${curso}&topico=${topico}&concluido=${concluido}`);
            }
            
            // Inicializar barra de progresso
            atualizarBarraProgresso();
        }
        
        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            inicializarTermos('<?= $curso ?>', <?= json_encode($progresso) ?>);
        });
    </script>
</body>
</html>