-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.0.30 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para athenaris_db
CREATE DATABASE IF NOT EXISTS `athenaris_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `athenaris_db`;

-- Copiando estrutura para tabela athenaris_db.cursos
CREATE TABLE IF NOT EXISTS `cursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text,
  `categoria` varchar(100) DEFAULT NULL,
  `ordem` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela athenaris_db.cursos: ~3 rows (aproximadamente)
INSERT INTO `cursos` (`id`, `slug`, `titulo`, `descricao`, `categoria`, `ordem`) VALUES
	(1, NULL, 'Termos Importantes', 'Aprenda os conceitos essenciais do mundo financeiro e seus significados.', NULL, 0),
	(2, NULL, 'Renda Fixa', 'Entenda como funcionam investimentos seguros e previsíveis.', NULL, 0),
	(3, NULL, 'Renda Variável', 'Descubra como investir em ações e ativos com rentabilidade variável.', NULL, 0);

-- Copiando estrutura para tabela athenaris_db.cursos_usuarios
CREATE TABLE IF NOT EXISTS `cursos_usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `sessao_id` int NOT NULL,
  `data_conclusao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`,`sessao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela athenaris_db.cursos_usuarios: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela athenaris_db.progresso_cursos
CREATE TABLE IF NOT EXISTS `progresso_cursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `curso` varchar(100) NOT NULL,
  `topico` int NOT NULL,
  `concluido` tinyint(1) DEFAULT '0',
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `progresso_cursos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela athenaris_db.progresso_cursos: ~19 rows (aproximadamente)
INSERT INTO `progresso_cursos` (`id`, `usuario_id`, `curso`, `topico`, `concluido`, `atualizado_em`) VALUES
	(1, 2, 'renda_variavel', 10, 0, '2025-10-23 23:31:20'),
	(2, 2, 'renda_fixa', 1, 1, '2025-10-24 00:57:07'),
	(3, 2, 'renda_fixa', 2, 1, '2025-10-24 00:57:09'),
	(4, 2, 'renda_fixa', 3, 1, '2025-10-24 00:57:09'),
	(5, 3, 'renda_fixa', 1, 0, '2025-10-24 11:14:12'),
	(6, 3, 'renda_fixa', 2, 0, '2025-10-24 11:14:11'),
	(7, 5, 'renda_variavel', 1, 1, '2025-10-24 12:09:20'),
	(8, 5, 'renda_variavel', 2, 1, '2025-10-24 12:09:23'),
	(9, 5, 'renda_variavel', 3, 1, '2025-10-24 12:09:24'),
	(10, 5, 'renda_variavel', 4, 1, '2025-10-24 12:09:25'),
	(11, 5, 'renda_variavel', 5, 1, '2025-10-24 12:09:27'),
	(12, 5, 'renda_variavel', 6, 1, '2025-10-24 12:09:28'),
	(13, 5, 'renda_variavel', 7, 1, '2025-10-24 12:09:30'),
	(14, 5, 'renda_variavel', 8, 1, '2025-10-24 12:09:31'),
	(15, 5, 'renda_variavel', 9, 1, '2025-10-24 12:09:33'),
	(16, 5, 'renda_variavel', 10, 1, '2025-10-24 12:09:34'),
	(17, 4, 'renda_fixa', 1, 1, '2025-10-24 13:49:02'),
	(18, 4, 'renda_fixa', 2, 1, '2025-10-24 14:07:36'),
	(19, 9, 'renda_fixa', 1, 1, '2025-10-24 14:31:12');

-- Copiando estrutura para tabela athenaris_db.sessoes
CREATE TABLE IF NOT EXISTS `sessoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `curso_id` int NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text,
  `video_url` varchar(255) DEFAULT NULL,
  `ordem` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `curso_id` (`curso_id`),
  CONSTRAINT `sessoes_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela athenaris_db.sessoes: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela athenaris_db.transacoes
CREATE TABLE IF NOT EXISTS `transacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `data_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `transacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela athenaris_db.transacoes: ~9 rows (aproximadamente)
INSERT INTO `transacoes` (`id`, `usuario_id`, `nome`, `valor`, `tipo`, `data_registro`) VALUES
	(2, 2, 'Salário', 2000.00, 'receita', '2025-10-19 00:17:41'),
	(3, 2, 'Conta de luz', 200.00, 'despesa', '2025-10-19 00:18:15'),
	(5, 2, 'Aluguel', 1000.00, 'receita', '2025-10-20 00:06:38'),
	(6, 2, 'Mercado', 800.00, 'despesa', '2025-10-20 00:06:55'),
	(7, 3, 'salario', 15000.00, 'receita', '2025-10-24 11:12:06'),
	(8, 3, 'academia', 100.00, 'despesa', '2025-10-24 11:13:03'),
	(9, 3, 'carro', 20000.00, 'despesa', '2025-10-24 11:13:34'),
	(12, 8, 'Bolsa Familia', 600.00, 'receita', '2025-10-24 13:11:14');

-- Copiando estrutura para tabela athenaris_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `perfil` enum('estudante','adulto') NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `foto_perfil` varchar(255) DEFAULT 'uploads/default.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela athenaris_db.usuarios: ~9 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `perfil`, `data_cadastro`, `foto_perfil`) VALUES
	(1, 'Teste', 'teste@gmail.com', '$2y$10$ynApLVrPr8nVkO1Q9FQWyuoHOC65Td1IVnQ/C5l48BoaNbVT0bWjO', 'estudante', '2025-10-15 17:48:58', 'uploads/default.png'),
	(2, 'Nicolas', 'nicolas@gmail.com', '$2y$10$mzgq2Mb94GtyEyYhC1EcI.o5fjQjDBVnSXTxRTNgEAKirzVMQTndm', 'estudante', '2025-10-18 18:34:55', 'uploads/perfil_2.jpg'),
	(3, 'Mateus', 'user@etec.sp.gov.br', '$2y$10$bMjrVrFuaWA6MiAoEZ4oo.cGRYEQmwTLtG7NsoVUuHMhHdUYi8jNy', 'estudante', '2025-10-24 11:10:37', 'uploads/default.png'),
	(4, 'Usuário', 'user@gmail.com', '$2y$10$0bqxUIhalxqT4ZTU1H7KxeKnQ302QMcSJqyLxNRRvCtneRmTu02nm', 'estudante', '2025-10-24 11:37:10', 'uploads/default.png'),
	(5, 'éoratãobolabola', 'user2@gmail.com', '$2y$10$OzDyAb/Rlu7o3Pzl1HbjXutkE9QCtby.gGSLHVlzeMbwLotbV/.ae', 'estudante', '2025-10-24 12:08:12', 'uploads/default.png'),
	(6, 'uer@gmail.com', 'uer@gmail.com', '$2y$10$1s1oLfUSJdkVnpXXK80T2uxp6wJ89pUeoK3xze916wc7TulpH068q', 'estudante', '2025-10-24 12:46:32', 'uploads/default.png'),
	(8, 'user@gmail.com', 'user111@gmail.com', '$2y$10$aF3xryIJintpIe1ZtGUs8uOcrl0LNhnK6P8VPlK2qxci0FxmB2Z.C', 'estudante', '2025-10-24 13:09:35', 'uploads/default.png'),
	(9, 'Ronaldo Junior', 'ronaldo@gmail.com', '$2y$10$xi4Ujm0rDujd/JrSuh5T1.0GQX4WKY17YCTxIBk81O5KNTWoIWmXm', 'estudante', '2025-10-24 14:27:43', 'uploads/default.png'),
	(10, 'Alex Aizza da Silva', 'bomdiA@gmail.com', '$2y$10$SOp2/yFAHTzcIUB68l/DKeZjNK3vzyhk.rH3krdzt4nB07nuoOK1C', 'estudante', '2025-10-24 14:59:03', 'uploads/default.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
