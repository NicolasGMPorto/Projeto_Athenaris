<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';
$usuario_id = $_SESSION['usuario_id'];
$mensagem_sucesso = '';
$mensagem_erro = '';

try {
    $stmt = $conexao->prepare("SELECT nome, email, foto_perfil FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // Garante que a sessão está sincronizada com o banco
    $_SESSION['nome_usuario'] = $usuario['nome'];
    $_SESSION['foto_usuario'] = $usuario['foto_perfil'] ?: 'uploads/default.png';

} catch (PDOException $e) {
    $mensagem_erro = "Erro ao carregar dados do usuário.";
}

// Processa ações de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['action'] ?? '';

    // Atualizar nome
    if ($acao === 'update_name') {
        $novo_nome = trim($_POST['nome'] ?? '');
        if ($novo_nome === '') {
            $mensagem_erro = "O nome não pode ser vazio.";
        } elseif ($novo_nome !== $usuario['nome']) {
            $stmt = $conexao->prepare("UPDATE usuarios SET nome = :nome WHERE id = :id");
            $stmt->bindParam(':nome', $novo_nome);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            $_SESSION['nome_usuario'] = $novo_nome;
            $usuario['nome'] = $novo_nome;
            $mensagem_sucesso = "Nome atualizado com sucesso!";
        }
    }

    // Atualizar senha
    elseif ($acao === 'update_password') {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirma_senha = $_POST['confirma_senha'] ?? '';

        if (empty($senha_atual) || empty($nova_senha) || empty($confirma_senha)) {
            $mensagem_erro = "Preencha todos os campos.";
        } elseif ($nova_senha !== $confirma_senha) {
            $mensagem_erro = "As senhas não coincidem.";
        } elseif (strlen($nova_senha) < 6) {
            $mensagem_erro = "A nova senha deve ter no mínimo 6 caracteres.";
        } else {
            $stmt = $conexao->prepare("SELECT senha_hash FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            $hash_atual = $stmt->fetchColumn();

            if (password_verify($senha_atual, $hash_atual)) {
                $nova_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("UPDATE usuarios SET senha_hash = :hash WHERE id = :id");
                $stmt->bindParam(':hash', $nova_hash);
                $stmt->bindParam(':id', $usuario_id);
                $stmt->execute();
                $mensagem_sucesso = "Senha alterada com sucesso!";
            } else {
                $mensagem_erro = "Senha atual incorreta.";
            }
        }
    }

    // Upload da foto
    elseif ($acao === 'update_photo' && isset($_FILES['foto'])) {
        $foto = $_FILES['foto'];
        if ($foto['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $novo_nome_arquivo = 'perfil_' . $usuario_id . '.' . $ext;
                $caminho_destino = 'uploads/' . $novo_nome_arquivo;

                if (!is_dir('uploads')) mkdir('uploads', 0755, true);

                // Remove imagem anterior se existir e for diferente da padrão
                if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil']) && $usuario['foto_perfil'] !== 'uploads/default.png') {
                    unlink($usuario['foto_perfil']);
                }

                move_uploaded_file($foto['tmp_name'], $caminho_destino);

                $stmt = $conexao->prepare("UPDATE usuarios SET foto_perfil = :foto WHERE id = :id");
                $stmt->bindParam(':foto', $caminho_destino);
                $stmt->bindParam(':id', $usuario_id);
                $stmt->execute();

                $usuario['foto_perfil'] = $caminho_destino;
                $_SESSION['foto_usuario'] = $caminho_destino; // sincroniza a sessão
                $mensagem_sucesso = "Foto de perfil atualizada!";
            } else {
                $mensagem_erro = "Envie apenas arquivos JPG, PNG ou WEBP.";
            }
        } else {
            $mensagem_erro = "Erro no envio da foto.";
        }
    }

    // Logout seguro
    elseif ($acao === 'logout') {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Athenaris</title>
    <link rel="stylesheet" href="CSS/home.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Merriweather+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        .perfil-container {
            grid-column: 1 / span 2;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .perfil-card {
            background-color: var(--cor-texto-claro);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid var(--cor-borda);
        }
        
        .perfil-titulo {
            color: var(--cor-primaria);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .perfil-email {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .perfil-secao {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--cor-borda);
        }
        
        .perfil-secao:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .perfil-subtitulo {
            color: var(--cor-primaria);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .foto-container {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 20px;
        }
        
        .foto-perfil-grande {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--cor-primaria);
        }
        
        .upload-foto {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--cor-texto-escuro);
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--cor-borda);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--cor-primaria);
        }
        
        .btn-perfil {
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            font-size: 1rem;
        }
        
        .btn-perfil:hover {
            background-color: #2a4a7a;
        }
        
        .btn-logout {
            background-color: #1E3A5F;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            font-size: 1rem;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-logout:hover {
            background-color: #2a4a7a;
        }
        
        .alerta {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
        
        .alerta.sucesso {
            background-color: #e0fff0;
            color: var(--cor-destaque-sucesso);
            border: 1px solid var(--cor-destaque-sucesso);
        }
        
        .alerta.erro {
            background-color: #ffe0e0;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        @media (max-width: 768px) {
            .foto-container {
                flex-direction: column;
                text-align: center;
            }
            
            .perfil-card {
                padding: 20px;
            }
        }

        .perfil-foto {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #1E3A5F;
}


.perfil-foto:hover {
    transform: scale(1.05);
}
    </style>
</head>
<body class="corpo-dashboard">

    <aside class="sidebar">
        <div class="logo-icon" title="Athenaris">
            <img src="imagens/athenaris_logo_tr.png" alt="Logo Athenaris" style="width: 50px; height: 50px;">
        </div>
        <nav class="nav-vertical">
            <a href="home.php" class="nav-link" title="Início">
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
        <div class="perfil-container">
            
            <?php if ($mensagem_erro): ?>
                <div class="alerta erro">
                    <?php echo htmlspecialchars($mensagem_erro); ?>
                </div>
            <?php elseif ($mensagem_sucesso): ?>
                <div class="alerta sucesso">
                    <?php echo htmlspecialchars($mensagem_sucesso); ?>
                </div>
            <?php endif; ?>

            <div class="perfil-card">
                <h1 class="perfil-titulo">Meu Perfil</h1>
                <p class="perfil-email">E-mail: <?php echo htmlspecialchars($usuario['email']); ?></p>
                
                <!-- Foto de Perfil -->
                <div class="perfil-secao">
                    <h2 class="perfil-subtitulo">Foto de Perfil</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_photo">
                        <div class="foto-container">
                            <img src="<?php echo htmlspecialchars($usuario['foto_perfil'] ?: 'uploads/default.png'); ?>" 
                                 alt="Foto de perfil" class="foto-perfil-grande">
                            <div class="upload-foto">
                                <input type="file" name="foto" accept="image/*" required class="form-input">
                                <button type="submit" class="btn-perfil">Atualizar Foto</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Nome -->
                <div class="perfil-secao">
                    <h2 class="perfil-subtitulo">Nome</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_name">
                        <div class="form-group">
                            <label class="form-label" for="nome">Nome:</label>
                            <input type="text" id="nome" name="nome" class="form-input"
                                   value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>
                        <button type="submit" class="btn-perfil">Salvar Nome</button>
                    </form>
                </div>

                <!-- Senha -->
                <div class="perfil-secao">
                    <h2 class="perfil-subtitulo">Alterar Senha</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_password">
                        <div class="form-group">
                            <label class="form-label" for="senha_atual">Senha Atual</label>
                            <input type="password" name="senha_atual" id="senha_atual" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nova_senha">Nova Senha</label>
                            <input type="password" name="nova_senha" id="nova_senha" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirma_senha">Confirmar Senha</label>
                            <input type="password" name="confirma_senha" id="confirma_senha" class="form-input" required>
                        </div>
                        <button type="submit" class="btn-perfil">Alterar Senha</button>
                    </form>
                </div>

                <!-- Logout -->
                <div class="perfil-secao">
                    <form method="POST">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn-logout">Sair da Conta</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos  
    </footer>

</body>
</html>