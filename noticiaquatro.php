<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html'); 
    exit;
}

require_once 'conexao.php'; 

$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';

$mensagem_sucesso = isset($_SESSION['mensagem_sucesso']) ? $_SESSION['mensagem_sucesso'] : null;
unset($_SESSION['mensagem_sucesso']);

// Dados da notícia (em um cenário real, viriam do banco de dados)
$noticia = [
    'titulo' => 'Casas Bahia anuncia parceria com Mercado Livre e ações disparam',
    'subtitulo' => 'Analistas do Santander avaliam que o acordo traz benefícios para ambos os lados, dada a proposta de valor complementar de cada empresa.',
    'data' => 'Por Reuters 23/10/2025 12h37',
    'imagem' => 'imagens/noticiaquatro.avif',
    'texto' => 'As ações da Casas Bahia subiram mais de 17% nesta quinta-feira, impulsionadas pelo anúncio de uma parceria com o Mercado Livre. A partir de novembro, a varejista começará a vender seus produtos na plataforma do marketplace, em um acordo comercial de longo prazo.

📱Baixe o app do g1 para ver notícias em tempo real e de graça
A notícia também trouxe pressão para os papéis do Magazine Luiza, concorrente no setor de eletrônicos e eletrodomésticos.

Por volta de 12h10, as ações da Casas Bahia avançavam 6,35%, a R$ 3,35, chegando a atingir R$ 3,69 na máxima do dia, o que representa uma alta de 17,1%.
Já os papéis do Mercado Livre se mantinham estáveis, em US$ 2.097,01.
Enquanto isso, as ações da Magazine Luiza caíam 6%, a R$ 7,84.

Executivos das duas empresas disseram que a expectativa é que a parceria aumente a participação do Mercado Livre nesses segmentos no Brasil, ao mesmo tempo em que ajuda a impulsionar as vendas da Casas Bahia.

Analistas do Santander avaliam que o acordo traz benefícios para ambos os lados.

"Considerando que a Casas Bahia já vem focando em categorias de bens duráveis nos últimos meses, os possíveis ganhos de escala em vendas obtidos ao alcançar um público mais amplo devem contribuir para uma maior alavancagem operacional e para melhores condições junto aos fornecedores", afirmaram.

Para o Mercado Livre, a parceria deixa a plataforma mais completa, adicionando um grande player nacional em categorias em que vendedores menores têm dificuldade de competir.

Magalu sob pressão
Apesar do efeito positivo para Casas Bahia e Mercado Livre, os analistas destacam que a Magazine Luiza pode sentir uma pressão extra nas vendas de produtos próprios, dado que a parceria fortalece a posição competitiva da Casas Bahia no marketplace.

Analistas do Citi colocaram o Magazine Luiza sob observação negativa por 30 dias após o anúncio, considerando que a parceria, junto com a concorrência intensa de Mercado Livre, Shopee e Amazon, aumenta a pressão sobre a empresa.
'
];

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notícias - Athenaris</title>
    <link rel="stylesheet" href="CSS/home.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Merriweather+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos específicos para a página de notícias */
        .main-content {
            display: block !important;
            padding: 30px;
        }
        
        .noticia-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 40px;
            background-color: var(--cor-texto-claro);
            border-radius: 10px;
            box-shadow: var(--cor-sombra);
            min-height: calc(100vh - 200px);
        }
        
        .noticia-titulo {
            font-family: 'Merriweather', serif;
            font-size: 2.5rem;
            color: var(--cor-texto-escuro);
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .noticia-subtitulo {
            font-size: 1.5rem;
            color: #666;
            margin-bottom: 25px;
            font-weight: 400;
            line-height: 1.4;
        }
        
        .noticia-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--cor-borda);
            color: #777;
            font-size: 1rem;
        }
        
        .noticia-imagem {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 35px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .noticia-texto {
            font-family: 'Merriweather', serif;
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--cor-texto-escuro);
            text-align: justify;
            max-width: 100%;
        }
        
        .noticia-texto p {
            margin-bottom: 25px;
        }
        
        .voltar-link {
            display: inline-block;
            margin-top: 40px;
            padding: 12px 25px;
            background-color: var(--cor-primaria);
            color: var(--cor-texto-claro);
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s;
            font-size: 1rem;
        }
        
        .voltar-link:hover {
            background-color: #2a4a7a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Ajustes para o layout grid */
        body.corpo-dashboard {
            grid-template-rows: 80px 1fr auto;
        }
        
        @media (max-width: 768px) {
            .noticia-container {
                padding: 20px;
            }
            
            .noticia-titulo {
                font-size: 2rem;
            }
            
            .noticia-subtitulo {
                font-size: 1.2rem;
            }
            
            .noticia-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .noticia-texto {
                font-size: 1.1rem;
            }
        }
    </style>
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

        <div class="noticia-container">
            <h1 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
            <h2 class="noticia-subtitulo"><?php echo htmlspecialchars($noticia['subtitulo']); ?></h2>
            
            <div class="noticia-meta">
                <span>Publicado em: <?php echo htmlspecialchars($noticia['data']); ?></span>
                <span>Por: Equipe Athenaris</span>
            </div>
            
            <img src="<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="Imagem da notícia" class="noticia-imagem" onerror="this.onerror=null;this.src='https://placehold.co/1200x600/1E3A5F/ffffff?text=Not%C3%ADcia+de+Investimentos';">
            
            <div class="noticia-texto">
                <?php 
                // Quebrar o texto em parágrafos
                $paragrafos = explode("\n\n", $noticia['texto']);
                foreach ($paragrafos as $paragrafo) {
                    echo '<p>' . nl2br(htmlspecialchars(trim($paragrafo))) . '</p>';
                }
                ?>
            </div>
            

    </main>

    <footer class="footer">
        | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos  
    </footer>

    <script src="js/home.js"></script> 
</body>
</html>