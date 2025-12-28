<?php
session_start();
require_once 'conexao.php';

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: cursos.php');
    exit;
}


$usuario_id = $_SESSION['usuario_id'];
$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';
$curso_id = $_GET['id'] ?? null;

if (!$curso_id) {
    echo "<h3>Curso não encontrado.</h3>";
    exit;
}

try {
    $stmt = $conexao->prepare("SELECT * FROM cursos WHERE id = :id");
    $stmt->bindParam(':id', $curso_id);
    $stmt->execute();
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo "<h3>Curso não encontrado.</h3>";
        exit;
    }

    $stmt = $conexao->prepare("
        SELECT s.*, 
               (SELECT COUNT(*) FROM cursos_usuarios 
                WHERE usuario_id = :usuario_id AND sessao_id = s.id) AS concluida
        FROM sessoes s
        WHERE s.curso_id = :curso_id
    ");
    $stmt->bindParam(':curso_id', $curso_id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $sessoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir'])) {
    $sessao_id = $_POST['sessao_id'];
    $stmt = $conexao->prepare("
        INSERT IGNORE INTO cursos_usuarios (usuario_id, sessao_id, data_conclusao)
        VALUES (:usuario_id, :sessao_id, NOW())
    ");
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':sessao_id', $sessao_id);
    $stmt->execute();
    header("Location: curso.php?id=" . $curso_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['titulo']); ?> - Athenaris</title>
    <link rel="stylesheet" href="css/cursos.css">
</head>
<body class="corpo-dashboard">

    <aside class="sidebar">
        <div class="logo-icon">
            <img src="imagens/athenaris_logo.png" alt="Logo Athenaris" width="50">
        </div>
        <nav class="nav-vertical">
            <a href="home.php" class="nav-link" title="Início"><img src="imagens/home_icone.png"></a>
            <a href="orcamento.php" class="nav-link" title="Orçamento"><img src="imagens/moedas_icone.webp"></a>
            <a href="#" class="nav-link" title="Gráficos"><img src="imagens/graficos_icone.png"></a>
            <a href="cursos.php" class="nav-link ativo" title="Cursos"><img src="imagens/livro_icone.png"></a>
            <a href="calculadora.php" class="nav-link" title="Calculadora"><img src="imagens/calculadora_icone.png"></a>
            <a href="investimentos.php" class="nav-link" title="Investimentos"><img src="imagens/acoes_icone.png"></a>
        </nav>
    </aside>

    <header class="header">
        <div class="user-info">
            <span>Bem-vindo, <?php echo strtoupper(htmlspecialchars($nome_usuario)); ?>!</span>
            <a href="perfil.php" class="perfil-link">
                <img src="<?php echo htmlspecialchars($foto_usuario); ?>" class="perfil-foto">
            </a>
        </div>
    </header>

    <main class="main-content">
        <h2 class="titulo-principal"><?php echo htmlspecialchars($curso['titulo']); ?></h2>
        <p><?php echo htmlspecialchars($curso['descricao']); ?></p>

        <div class="lista-sessoes">
            <?php foreach ($sessoes as $sessao): ?>
            <div class="sessao-card <?php echo $sessao['concluida'] ? 'concluida' : ''; ?>">
                <h4><?php echo htmlspecialchars($sessao['titulo']); ?></h4>
                <p><?php echo htmlspecialchars($sessao['conteudo']); ?></p>
                <?php if (!$sessao['concluida']): ?>
                    <form method="POST">
                        <input type="hidden" name="sessao_id" value="<?php echo $sessao['id']; ?>">
                        <button type="submit" name="concluir" class="btn-concluir">Marcar como concluído</button>
                    </form>
                <?php else: ?>
                    <span class="badge">✔ Concluído</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos
    </footer>

</body>
</html>
