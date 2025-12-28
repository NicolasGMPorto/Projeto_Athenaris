<?php
session_start();
require_once 'conexao.php'; // O arquivo conexao.php deve estar na raiz.

$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $mensagem_erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            $stmt = $conexao->prepare("SELECT id, nome, senha_hash FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome_usuario'] = $usuario['nome'];
                
                header('Location: home.php');
                exit;

            } else {
                $mensagem_erro = "Email ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $mensagem_erro = "Erro interno ao tentar fazer login. Tente novamente mais tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Athenaris</title>
    <link rel="stylesheet" href="CSS/login.css"> <!-- Certifique-se de que este CSS existe --><link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Merriweather+Sans:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="corpo-pagina-autenticacao">

    <div class="cartao-autenticacao">
        <h2 class="titulo cor-primaria-texto">Bem-vindo de volta!</h2>
        
        <?php if ($mensagem_erro): ?>
            <p id="exibir-mensagem" class="erro"><?php echo htmlspecialchars($mensagem_erro); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" class="campo-input" placeholder="Seu e-mail" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            <input type="password" name="senha" class="campo-input" placeholder="Sua senha" required>
            
            <button type="submit" class="btn-principal btn-grande">Entrar</button>
        </form>

        <a href="cadastro.php" class="link-alternancia">Ainda não tem conta? Cadastre-se</a>
    </div>

</body>
</html>
