<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html'); 
    exit;
}

require_once 'conexao.php'; 

$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';
$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? null;
unset($_SESSION['mensagem_sucesso']);

// =====================
// 🔹 CALCULA O SALDO ATUAL DO USUÁRIO
// =====================
$usuario_id = $_SESSION['usuario_id'];

$stmtSaldo = $conexao->prepare("
    SELECT 
        SUM(CASE WHEN tipo = 'receita' THEN valor ELSE -valor END) AS saldo
    FROM transacoes
    WHERE usuario_id = :uid
");
$stmtSaldo->bindParam(':uid', $usuario_id);
$stmtSaldo->execute();
$resultadoSaldo = $stmtSaldo->fetch(PDO::FETCH_ASSOC);

$patrimonio_atual = $resultadoSaldo['saldo'] ?? 0.00;
$patrimonio_formatado = 'R$ ' . number_format($patrimonio_atual, 2, ',', '.');

// =====================
// 🔹 PROGRESSO NO CURSO
// =====================
$stmt = $conexao->prepare("
    SELECT c.titulo AS curso_nome,
           COUNT(DISTINCT s.id) AS total_aulas,
           COUNT(DISTINCT cu.sessao_id) AS aulas_concluidas
    FROM cursos c
    LEFT JOIN sessoes s ON s.curso_id = c.id
    LEFT JOIN cursos_usuarios cu ON cu.sessao_id = s.id AND cu.usuario_id = :uid
    GROUP BY c.id
    ORDER BY c.id
    LIMIT 1
");
$stmt->bindParam(':uid', $_SESSION['usuario_id']);
$stmt->execute();
$curso_atual = $stmt->fetch(PDO::FETCH_ASSOC);

$nome_curso_atual = $curso_atual['curso_nome'] ?? 'Curso não iniciado';
$progresso_curso = ($curso_atual['total_aulas'] ?? 0) > 0
    ? round(($curso_atual['aulas_concluidas'] / $curso_atual['total_aulas']) * 100)
    : 0;

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="CSS/home.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Merriweather+Sans:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="corpo-dashboard">

    <aside class="sidebar">
        <div class="logo-icon" title="Athenaris">
            <img src="imagens/athenaris_logo_tr.png" alt="Logo Athenaris" style="width: 50px; height: 50px;">
        </div>
        <nav class="nav-vertical">
            <a href="orcamento.php" class="nav-link ativo" title="Início">
                <img src="imagens/home_icone.png" alt="Início">
            </a>
            <a href="orcamento.php" class="nav-link" title="Orçamento">
                <img src="imagens/moedas_icone.webp" alt="Orçamento">
            </a>
            <a href="cursos.php" class="nav-link" title="Lições">
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

<header class="header">
    <div class="user-info">
        <span>Bem-vindo, <?php echo strtoupper(htmlspecialchars($nome_usuario)); ?>!</span>
        <a href="perfil.php" class="perfil-link" title="Acessar Perfil"> 
            <img src="<?php echo htmlspecialchars($foto_usuario); ?>" alt="Foto de perfil" class="perfil-foto">
        </a> 
    </div>
</header>

<main class="main-content">
        
    <?php if ($mensagem_sucesso): ?>
        <div class="alerta-sucesso">
            <p id="exibir-mensagem">
                <?php echo htmlspecialchars($mensagem_sucesso); ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="card-patrimonio">
        <h3>Saldo disponível</h3>
        <div class="valor">
            <img src="imagens/seta_crescimento.png" alt="Trend" style="width: 20px; height: 20px; margin-right: 8px;">
            <span><?php echo $patrimonio_formatado; ?></span>
        </div>
    </div>
        
    <div class="card-acao">
        <a href="orcamento.php">Criar orçamento simples</a>
    </div>

    <div class="card-acao">
        <a href="calculadora.php">Simular juros</a>
    </div>

    <div class="card-acao">
        <a href="cursos.php">Comece a investir</a>
    </div>


    <section class="market-news">
        <center><h3>Novidades do mercado</h3></center>
        <div class="news-container">
            <a href="noticiaum.php" class="news-item" title="Valor de empréstimos consignados para aposentados caiu para menos da metade após exigência de biometria">
                <img src="imagens/noticiaum.jpg" alt="Gráfico de queda." onerror="this.onerror=null;this.src='https://placehold.co/300x180/008080/ffffff?text=Not%C3%ADcia+1';">
            </a>
            <a href="noticiadois.php" class="news-item" title="Milei escolhe nome forte das Finanças como novo chanceler da Argentina">
                <img src="imagens/noticiadois.avif" alt="Análise de mercado." onerror="this.onerror=null;this.src='https://placehold.co/300x180/00A36C/ffffff?text=Not%C3%ADcia+2';">
            </a>
            <a href="noticiatres.php" class="news-item" title="Netflix sofre impacto bilionário por disputa tributária no Brasil">
                <img src="imagens/noticiatres.webp" alt="Dicas de investimento." onerror="this.onerror=null;this.src='https://placehold.co/300x180/FFCC00/333333?text=Not%C3%ADcia+3';">
            </a>
            <a href="noticiaquatro.php" class="news-item" title="Casas Bahia anuncia parceria com Mercado Livre e ações disparam">
                <img src="imagens/noticiaquatro.avif" alt="Dicas de investimento." onerror="this.onerror=null;this.src='https://placehold.co/300x180/FFCC00/333333?text=Not%C3%ADcia+4';">
            </a>
            <a href="noticiacinco.php" class="news-item" title="Crise global de chips ameaça paralisar montadoras no Brasil em semanas, alerta associação">
                <img src="imagens/noticiacinco.avif" alt="Dicas de investimento." onerror="this.onerror=null;this.src='https://placehold.co/300x180/FFCC00/333333?text=Not%C3%ADcia+5';">
            </a>
            <a href="noticiaseis.php" class="news-item" title="Estados e municípios terão de adotar critérios de transparência para executar emendas em 2026, diz Dino">
                <img src="imagens/noticiaseis.avif" alt="Dicas de investimento." onerror="this.onerror=null;this.src='https://placehold.co/300x180/FFCC00/333333?text=Not%C3%ADcia+6';">
            </a>
        </div>
    </section>

</main>

<footer class="footer">
    | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos  
</footer>

<script src="js/home.js"></script> 
</body>
</html>
