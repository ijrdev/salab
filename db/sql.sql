-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.7.28-0ubuntu0.18.04.4 - (Ubuntu)
-- OS do Servidor:               Linux
-- HeidiSQL Versão:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando dados para a tabela salab2.tb_grupos: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_grupos` DISABLE KEYS */;
INSERT INTO `tb_grupos` (`id_grupo`, `grupo`) VALUES
	(1, 'Application\\Controller\\AdministradorController');
/*!40000 ALTER TABLE `tb_grupos` ENABLE KEYS */;

-- Copiando dados para a tabela salab2.tb_laboratorios: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_laboratorios` DISABLE KEYS */;
INSERT INTO `tb_laboratorios` (`id_laboratorio`, `lab`, `tipo`, `descricao`) VALUES
	(1, 'LAB 11', 'ANÁLISES E MEDIDAS (LAMP)', '1° Andar (Lob/Lamp)'),
	(2, 'LAB 16', 'ANÁLISES CLÍNICAS', '1° Andar'),
	(3, 'LAB 05', 'ANATOMIA SINTÉTICA', 'Térreo (Anatomia 3)');
/*!40000 ALTER TABLE `tb_laboratorios` ENABLE KEYS */;

-- Copiando dados para a tabela salab2.tb_usuarios: ~2 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_usuarios` DISABLE KEYS */;
INSERT INTO `tb_usuarios` (`id_usuario`, `matricula`, `email`, `senha`, `id_grupo`) VALUES
	(3, 20191, 'adm@gmail.com', '$2y$10$xKhneSRWzwOB1.pxV4t.yura1C.De2X/XMAAnTz2lvgHXj.ko.12a', 1),
	(5, 20195, 'a@gmail.com', '$2y$10$LEPpgA2ec63jb7pTfMGJmeibgaoHYcpLaZaCAgZjD1Nz/ACl0DKOC', 1);
/*!40000 ALTER TABLE `tb_usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
