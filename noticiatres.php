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
    'titulo' => 'Netflix sofre impacto bilionário por disputa tributária no Brasil',
    'subtitulo' => 'Empresa teve lucro abaixo da meta e alegou que um processo "em andamento" no país a obrigou a registrar uma despesa de US$ 619 milhões (R$ 3,3 bilhões) no balanço do terceiro trimestre.',
    'data' => 'Por Micaela Santos, g1 — São Paulo',
    'imagem' => 'imagens/noticiatres.webp',
    'texto' => 'Uma disputa envolvendo a cobrança de impostos no Brasil chamou atenção durante a divulgação do balanço financeiro mais recente da Netflix.

Na terça-feira (21), a plataforma de streaming anunciou que teve lucro de US$ 2,5 bilhões no mundo entre julho e setembro, abaixo dos US$ 3 bilhões que os analistas esperavam.

Com isso, as ações da companhia recuaram, e o valor de mercado da Netflix passou de US$ 527 bilhões (R$ 2,8 trilhões) para US$ 494 bilhões (R$ 2,6 trilhões) no mesmo dia.

A Netflix normalmente não divulga dados de quanto fatura no Brasil. Mas, desta vez, explicou a investidores que uma cobrança de impostos no país afetou seus resultados. Entenda o caso abaixo.

O impacto tributário do Brasil na Netflix
A empresa afirmou que o resultado veio abaixo do esperado devido a uma disputa tributária "em andamento" no país, que a obrigou a registrar uma despesa de US$ 619 milhões (cerca de R$ 3,3 bilhões) no terceiro trimestre.

O caso envolve a Contribuição de Intervenção no Domínio Econômico (Cide), um imposto cuja aplicação foi ampliada por decisão do Supremo Tribunal Federal (STF) em agosto, afetando também outras empresas.

 A Cide é um tributo federal usado para regular setores da economia e financiar políticas públicas específicas. Um exemplo é a Cide-Combustíveis, cobrada sobre petróleo e derivados para bancar obras de transporte e programas ambientais.
 No caso da Netflix, o impasse envolve a cobrança da Cide Royalties, conhecida também como Cide-Tecnologia — uma taxa sobre pagamentos ao exterior ligados ao uso de tecnologia. O objetivo é estimular a inovação nacional e aumentar a arrecadação.
 Nos serviços de streaming, a Cide não incide sobre a assinatura em si, mas sobre a remessa de dinheiro ao exterior — ou seja, pagamentos feitos por empresas estrangeiras por serviços digitais.
🇧🇷 O imposto é cobrado desde 2001 no Brasil. Com isso, a importação de serviços e tecnologias fica sujeita a um pagamento de 10% sobre a remessa ao exterior.

"O impacto acumulado dessa despesa (aproximadamente 20% referente a 2025 e o restante ao período de 2022 a 2024) reduziu nossa margem operacional em mais de cinco pontos percentuais no terceiro trimestre", afirmou a Netflix no balanço.

Em agosto deste ano, o STF decidiu manter a constitucionalidade da Cide sobre remessas ao exterior, abrangendo qualquer tipo de contrato, incluindo serviços administrativos e direitos autorais, e não apenas a importação de tecnologia.
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