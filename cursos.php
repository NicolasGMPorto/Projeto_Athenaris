<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cursos</title>
<link rel="stylesheet" href="css/home.css">
<link rel="stylesheet" href="css/cursos.css">
</head>
<body class="corpo-dashboard">

<aside class="sidebar">
    <div class="logo-icon">
        <img src="imagens/athenaris_logo.png" alt="Logo" style="width:50px;height:50px;">
    </div>
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

<header class="header">
<div class="user-info">
    <span>Bem-vindo, <?php echo strtoupper(htmlspecialchars($nome_usuario)); ?>!</span>
    <a href="perfil.php" class="perfil-link"><img src="<?php echo htmlspecialchars($foto_usuario); ?>" class="perfil-foto"></a>
</div>
</header>

<main class="main-content-cursos">
    <h2 class="titulo">Cursos Financeiros</h2>
    <p class="subtitulo">Escolha um curso para começar ou continuar seu aprendizado:</p>

    <div class="cursos-container">
        <div class="curso-card">
            <h3>📘 Termos Importantes</h3>
            <p>Aprenda os conceitos fundamentais do mundo financeiro e seus significados.</p>
            <a href="curso_termos.php" class="botao-acessar">Acessar curso</a>
        </div>

        <div class="curso-card">
            <h3>💰 Renda Fixa</h3>
            <p>Entenda como funcionam investimentos seguros como CDB, Tesouro Direto e CDI.</p>
            <a href="curso_rendafixa.php" class="botao-acessar">Acessar curso</a>
        </div>

        <div class="curso-card">
            <h3>📈 Renda Variável</h3>
            <p>Aprenda sobre ações, fundos imobiliários e investimentos com maior rentabilidade.</p>
            <a href="curso_rendavariavel.php" class="botao-acessar">Acessar curso</a>
        </div>
    </div>
</main>

<footer class="footer">
    | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos
</footer>
</body>
</html>
