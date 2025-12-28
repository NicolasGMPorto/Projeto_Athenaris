<?php
// Inicia a sessão para armazenar mensagens de status e login
session_start();

// Inclui a conexão com o banco de dados
require_once 'conexao.php'; // Verifique se o caminho está correto

$mensagem_status = null;

// --- INÍCIO DA LÓGICA DE PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha_pura = $_POST['senha'] ?? ''; 
    $confirma_senha = $_POST['confirma_senha'] ?? ''; 

    $erro_encontrado = false;

    // 1. VALIDAÇÕES
    if (empty($nome) || empty($email) || empty($senha_pura) || empty($confirma_senha)) {
        $erro_encontrado = true;
        $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Todos os campos são obrigatórios.'];
    } elseif ($senha_pura !== $confirma_senha) {
        $erro_encontrado = true;
        $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'As senhas não coincidem.'];
    } elseif (strlen($senha_pura) < 6) {
        $erro_encontrado = true;
        $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'A senha deve ter pelo menos 6 caracteres.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro_encontrado = true;
        $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Formato de e-mail inválido.'];
    }

    if (!$erro_encontrado) {
        // 2. VERIFICAÇÃO DE E-MAIL JÁ EXISTENTE (USANDO PDO)
        try {
            $sql_check_email = "SELECT id FROM usuarios WHERE email = :email";
            $stmt_check_email = $conexao->prepare($sql_check_email);
            $stmt_check_email->bindParam(':email', $email);
            $stmt_check_email->execute();

            if ($stmt_check_email->rowCount() > 0) {
                $erro_encontrado = true;
                $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Este e-mail já está cadastrado.'];
            }

        } catch (PDOException $e) {
            $erro_encontrado = true;
            $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Erro interno ao verificar o e-mail.'];
            error_log("Erro PDO: " . $e->getMessage());
        }
    }

    if (!$erro_encontrado) {
        // 3. INSERÇÃO E SESSÃO (USANDO PDO)
        try {
            $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT); 
            $sql_insert = "INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)";
            $stmt = $conexao->prepare($sql_insert);

            if ($stmt->execute([':nome' => $nome, ':email' => $email, ':senha_hash' => $senha_hash])) {
                $usuario_id = $conexao->lastInsertId();
                
                // Inicia Sessão e Redireciona para a área logada
                $_SESSION['usuario_id'] = $usuario_id;
                $_SESSION['nome_usuario'] = $nome;
                
                header('Location: home.php');
                exit;

            } else {
                $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Erro ao cadastrar: A inserção falhou.'];
            }

        } catch (PDOException $e) {
            $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Erro fatal de inserção: Tente novamente mais tarde.'];
            error_log("Erro PDO Cadastro: " . $e->getMessage());
        }
    }
    
    // Redireciona para a própria página para limpar o POST (PRG Pattern) e exibir a mensagem
    header('Location: cadastro.php');
    exit;
}
// --- FIM DA LÓGICA DE PROCESSAMENTO DO FORMULÁRIO ---


// Lógica para exibir a mensagem após o redirecionamento
if (isset($_SESSION['mensagem_status'])) {
    $mensagem_status = $_SESSION['mensagem_status'];
    unset($_SESSION['mensagem_status']);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Athenaris</title>
    <link rel="stylesheet" href="CSS/index.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Merriweather+Sans:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="corpo-pagina-autenticacao">

    <div class="cartao-autenticacao">
        <h2 class="titulo cor-primaria-texto">Crie Sua Conta Athenaris</h2>
        <p>Comece sua jornada de educação financeira.</p>

        <form id="cadastro-form" action="cadastro.php" method="POST">
            
            <!-- Exibe a mensagem de status da sessão, se houver -->
            <?php if ($mensagem_status): ?>
                <p id="exibir-mensagem" class="<?php echo $mensagem_status['tipo']; ?>">
                    <?php echo htmlspecialchars($mensagem_status['texto']); ?>
                </p>
            <?php endif; ?>
            <!-- Fim da mensagem de status -->

            <input type="text" id="nome" name="nome" class="campo-input" placeholder="Nome Completo" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
            
            <input type="email" id="email" name="email" class="campo-input" placeholder="E-mail" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            
            <input type="password" id="senha" name="senha" class="campo-input" placeholder="Senha (mínimo 6 caracteres)" required>
            
            <input type="password" id="confirma_senha" name="confirma_senha" class="campo-input" placeholder="Confirmar Senha" required>
            
            <button type="submit" class="btn-principal btn-grande" id="submit-btn">Cadastrar</button>

        </form>
        
        <a href="login.php" class="link-alternancia">Já tem uma conta? Faça Login</a>
    </div>
    
</body>
</html>
