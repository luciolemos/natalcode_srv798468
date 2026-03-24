-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: cede
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookshop_categories`
--

DROP TABLE IF EXISTS `bookshop_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_bookshop_categories_name` (`name`),
  KEY `idx_bookshop_categories_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_categories`
--

LOCK TABLES `bookshop_categories` WRITE;
/*!40000 ALTER TABLE `bookshop_categories` DISABLE KEYS */;
INSERT INTO `bookshop_categories` VALUES (1,'estudo-intermediario','Estudo (Intermediário)','Categoria de livros utilizada em cursos para a formação espiritual intermediária do cediano.',1,'2026-03-21 22:20:01','2026-03-22 14:59:42'),(3,'estudo-avancado','Estudo (Avançado)','Categoria de livros utilizada em cursos para a formação espiritual avancada do cediano.',1,'2026-03-22 11:30:39','2026-03-22 15:00:34'),(5,'estudo-jedi','Estudo (Jedi)','Categoria de livros utilizada em cursos para a formação espiritual nível jedi do cediano.',1,'2026-03-22 11:32:01','2026-03-22 15:03:33'),(7,'estudo-basico','Espiritualismo  e Filosofia Espiritual','Categoria de livros utilizada em cursos para a formação espiritual básica do cediano.',1,'2026-03-22 11:38:57','2026-03-23 22:49:34'),(13,'estudo-mediunico','Estudos Mediúnicos','Mediunidade é a capacidade que algumas pessoas têm de: \r\n👉 perceber ou se comunicar com espíritos;\r\n👉 Estudos mediúnicos são o estudo e desenvolvimento da mediunidade',1,'2026-03-22 15:18:14','2026-03-22 17:31:12'),(14,'espiritismo-experimental','Espiritismo Experimental','É o estudo baseado na observação direta dos fenômenos espirituais, especialmente os fenômenos mediúnicos.',1,'2026-03-22 17:29:53','2026-03-22 17:30:49'),(21,'evangelho-no-lar','Evangelho no Lar','Titulos de apoio para rotina de estudo e prece em familia.',1,'2026-03-22 20:18:32','2026-03-22 20:18:32'),(22,'formacao-de-trabalhadores','Formação de Trabalhadores','Materiais de apoio para equipes e servico voluntario.',1,'2026-03-22 20:18:32','2026-03-22 20:18:32'),(23,'infancia-e-juventude','Infância e Juventude','Livros voltados para publico infantil e juvenil.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(24,'familia-e-relacionamentos','Família e Relacionamentos','Leituras sobre convivencia, educacao e cuidado no lar.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(25,'mediunidade-pratica','Mediunidade Prática','Obras para estudo serio da mediunidade e da disciplina mediunica.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(26,'pesquisa-e-referencia','Pesquisa e Referência','Material de consulta, apoio a pesquisa e referencia doutrinaria.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(27,'acolhimento-e-servico','Acolhimento e Serviço','Livros para acolhimento fraterno, servico e consolacao.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(28,'evangelizacao-infantil','Evangelização infantil',NULL,1,'2026-03-23 12:36:24','2026-03-23 12:36:24'),(29,'estudos-policiais','Estudo polociais',NULL,1,'2026-03-23 18:53:32','2026-03-23 18:53:32');
/*!40000 ALTER TABLE `bookshop_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_genres`
--

DROP TABLE IF EXISTS `bookshop_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_genres` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_bookshop_genres_name` (`name`),
  KEY `idx_bookshop_genres_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_genres`
--

LOCK TABLES `bookshop_genres` WRITE;
/*!40000 ALTER TABLE `bookshop_genres` DISABLE KEYS */;
INSERT INTO `bookshop_genres` VALUES (1,'filosofia-espiritual','Filosofia espiritual',NULL,1,'2026-03-22 15:05:08','2026-03-22 15:23:54'),(2,'psicologia','Psicologia',NULL,1,'2026-03-22 15:05:28','2026-03-22 15:05:28'),(3,'romance','Romance',NULL,1,'2026-03-22 15:05:56','2026-03-22 15:05:56'),(4,'ficcao','Ficção',NULL,1,'2026-03-22 15:06:21','2026-03-22 15:06:21'),(5,'psicografia','Psicografia',NULL,1,'2026-03-22 15:06:48','2026-03-22 15:06:48'),(6,'economia','Economia',NULL,1,'2026-03-22 15:07:08','2026-03-22 15:07:08'),(7,'politica','Política',NULL,1,'2026-03-22 15:07:24','2026-03-22 15:07:24'),(8,'drama','Drama',NULL,1,'2026-03-22 15:07:36','2026-03-22 15:07:36'),(9,'poesia','Poesia',NULL,1,'2026-03-22 15:07:48','2026-03-22 15:07:48'),(10,'literatura-espiritualista','Literatura espiritualista',NULL,1,'2026-03-22 15:16:58','2026-03-22 16:25:27'),(11,'doutrinario','Doutrinário','Obras de estudo e fundamentacao doutrinaria.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(12,'biografia','Biografia','Relatos de vida, memoria e trajetorias inspiradoras.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(13,'infantojuvenil','Infantojuvenil','Livros de linguagem acessivel para criancas e jovens.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(14,'cronica','Filosófico, cientifico e Religioso','Textos breves, observacoes do cotidiano e reflexoes.',1,'2026-03-22 20:18:33','2026-03-23 22:46:23'),(15,'novela','Novela','Narrativas curtas para leitura individual ou em grupo.',1,'2026-03-22 20:18:33','2026-03-23 18:52:07'),(16,'mensagens','Mensagens','Coletaneas de paginas inspirativas e consoladoras.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(17,'referencia','Referência','Guias, consultas e material de referencia para estudos.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33'),(18,'estudo-tematico','Estudo Temático','Obras organizadas por tema, assunto ou modulo de estudo.',1,'2026-03-22 20:18:33','2026-03-22 20:18:33');
/*!40000 ALTER TABLE `bookshop_genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_collections`
--

DROP TABLE IF EXISTS `bookshop_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_collections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_bookshop_collections_name` (`name`),
  KEY `idx_bookshop_collections_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_collections`
--

LOCK TABLES `bookshop_collections` WRITE;
/*!40000 ALTER TABLE `bookshop_collections` DISABLE KEYS */;
INSERT INTO `bookshop_collections` VALUES (1,'estudos-da-codificacao','Estudos da Codificação','Coleção dedicada ao estudo progressivo dos princípios fundamentais da Doutrina Espírita.',1,'2026-03-22 20:56:06','2026-03-22 20:56:06'),(2,'saude','Saúde','Saúde física e mental.',1,'2026-03-22 20:56:06','2026-03-24 12:22:35'),(3,'evangelho-no-lar-e-no-coracao','Evangelho no Lar e no Coração','Volumes voltados à vivência do Evangelho no lar, na família e na convivência diária.',1,'2026-03-22 20:56:06','2026-03-22 20:56:06'),(4,'cadernos-de-reforma-intima','Cadernos de Reforma Íntima','Coleção de apoio à autoeducação moral, vigilância e crescimento espiritual.',1,'2026-03-22 20:56:06','2026-03-22 20:56:06'),(5,'obras-basicas','Obras Básicas','Série com leituras sobre imortalidade, reencarnação e continuidade da vida.',1,'2026-03-22 20:56:06','2026-03-23 22:43:20'),(6,'caminhos-da-caridade','Caminhos da Caridade','Coleção voltada ao serviço, acolhimento fraterno e prática do bem.',1,'2026-03-22 20:56:06','2026-03-22 20:56:06'),(7,'trilhas-do-trabalhador-espirita','Trilhas do Trabalhador Espírita','Materiais em sequência para formação e sustentação do serviço voluntário.',1,'2026-03-22 20:56:06','2026-03-22 20:56:06'),(8,'consolacao-e-esperanca','Consolação e Esperança','Coleção de leitura edificante, consolo espiritual e fortalecimento da fé raciocinada.',1,'2026-03-22 20:56:06','2026-03-22 20:56:06'),(9,'vida-no-mundo','Vida no Mundo Espiritual','Relatos de André Luiz',1,'2026-03-23 18:47:21','2026-03-23 18:48:22'),(10,'motiva-o','Motivação e Espiritualidade',NULL,1,'2026-03-24 11:06:32','2026-03-24 11:06:32'),(11,'alma-dos-animais','Alma dos Animais',NULL,1,'2026-03-24 11:38:24','2026-03-24 11:38:24'),(12,'magnetismo','Magnetismo',NULL,1,'2026-03-24 11:41:43','2026-03-24 11:41:43');
/*!40000 ALTER TABLE `bookshop_collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_books`
--

DROP TABLE IF EXISTS `bookshop_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `category_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genre_id` bigint unsigned DEFAULT NULL,
  `genre_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_id` bigint unsigned DEFAULT NULL,
  `collection_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isbn` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edition_label` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volume_number` smallint unsigned DEFAULT NULL,
  `volume_label` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_year` smallint unsigned DEFAULT NULL,
  `page_count` int unsigned DEFAULT NULL,
  `language` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image_mime_type` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image_size_bytes` bigint unsigned DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int NOT NULL DEFAULT '0',
  `stock_minimum` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `location_label` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `idx_bookshop_books_title` (`title`),
  KEY `idx_bookshop_books_author` (`author_name`),
  KEY `idx_bookshop_books_category` (`category_name`),
  KEY `idx_bookshop_books_status` (`status`),
  KEY `idx_bookshop_books_stock` (`stock_quantity`),
  KEY `idx_bookshop_books_category_id` (`category_id`),
  KEY `idx_bookshop_books_genre_id` (`genre_id`),
  KEY `idx_bookshop_books_genre` (`genre_name`),
  KEY `idx_bookshop_books_barcode` (`barcode`),
  KEY `idx_bookshop_books_collection_id` (`collection_id`),
  KEY `idx_bookshop_books_collection` (`collection_name`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_books`
--

LOCK TABLES `bookshop_books` WRITE;
/*!40000 ALTER TABLE `bookshop_books` DISABLE KEYS */;
INSERT INTO `bookshop_books` VALUES (66,'SEED-LIV-064','seed-livraria-item-064',24,'Família e Relacionamentos',11,'Doutrinário',3,'Evangelho no Lar e no Coração','A cada um Segundo suas OBRAS',NULL,'Francisco Faustino Costa','O Clarim','6588278446','978-6588278444','1ª Edição',1,'Relações',2025,NULL,'Português','Aborto provocado, prostituição, exploração e destruição de famílias, uso de drogas, marginalização, roubos... Vários atos, colocados em prática pelo uso leviano do livre-arbítrio, podem gerar consequências amargas para quem os pratica, nesta ou noutras vidas. Ao contar a trajetória e os dramas existenciais de três jovens, este romance espírita introduz o conceito de “viagens astrais” (desdobramento ou emancipação da alma) e mostra que não conseguimos fugir dos efeitos de atitudes impulsivas e impensadas. Como disse Jesus: “A cada um segundo suas obras”.','media/livraria/capas/cover_20260324143342_21da15db.jpg','image/jpeg',141900,0.10,35.00,19,1,'active','Estante D6','2026-03-22 20:18:33','2026-03-24 14:33:42'),(72,'SEED-LIV-070','seed-livraria-item-070',22,'Formação de Trabalhadores',5,'Psicografia',8,'Consolação e Esperança','A CAMINHO DA LUZ','Caderno de apoio para reuniões de estudo e serviço','FRANCISCO CÃ‚NDIDO XAVIER','FEB','6555706511','978-6555706512','Padão',1,NULL,2024,NULL,'Português','Objetivando orientar o homem de acordo com os desígnios da Misericórdia Divina, apresentando reflexões sobre as situações cotidianas à guisa dos ensinamentos e bondade celestes, A caminho da luz é obra merecedora de leitura e estudo para os que buscam compreender nosso mundo. Nesta inestimável obra, o Espírito Emmanuel narra a história da Humanidade sob a luz do Espiritismo, apresentando-nos acontecimentos e experiências que vão desde a gênese planetária até as perspectivas para o futuro da Humanidade, elucidando-nos a posição e a importância do Evangelho do Cristo diante da ciência, das religiões e das filosofias terrenas.','media/livraria/capas/cover_20260324125855_2df450b1.jpg','image/jpeg',25277,25.10,39.50,7,1,'active','Estante Q2','2026-03-22 20:18:33','2026-03-24 12:58:55'),(112,'10 RAZOES','vida-no-mundo',1,'Estudo (Intermediário)',10,'Literatura espiritualista',10,'Motivação e Espiritualidade','10 RAZÕES PARA SER ESPÍRITA',NULL,'José Carlos Leal','Novo Ser','978-85-6396-417-5','978-8563964175','3ª',1,NULL,2020,176,'Português','Como escolher a melhor religião diante de tantas seitas, cultos, crenças e igrejas? Em ?10 RAZÕES PARA SER ESPÍRITA? José Carlos Leal explica por que escolheu a Doutrina Espírita como religião. Neste livro o autor apresenta as dez questões que o fizeram passar do Materialismo para o Espiritismo. ?10 RAZÕES PARA SER ESPÍRITA? não têm por finalidade apresentar o Espiritismo como a melhor religião ou converter alguém, mas apenas mostrar que a verdadeira religião é a reforma íntima dos indivíduos, o que não se consegue com dogmas, rituais, culto e outras formas exteriores de religiosidade.','media/livraria/capas/cover_20260324144841_b07de69d.jpg','image/jpeg',40842,0.01,30.00,1,1,'active','2','2026-03-24 11:18:15','2026-03-24 14:48:41'),(113,'A ALMA DOS ANIMAIS','a-alma',7,'Espiritualismo  e Filosofia Espiritual',10,'Literatura espiritualista',8,'Consolação e Esperança','A ALMA DOS ANIMAIS',NULL,'Ernesto Bozzano','Editora do Conhecimento','978-65-5727-114-8','978-6557271148','Padão',1,NULL,2021,NULL,'Português','Em todas as épocas e lugares, foram registrados fenômenos suprafísicos – à semelhança dos casos humanos – envolvendo diferentes animais. Tradições locais, relatos diversos, sempre deram notícia deles. Mas não tinha havido uma tentativa séria de classificá-los e investigar esse ramo instigante da fenomenologia, dita metapsíquica, até que Ernesto Bozzano, o famoso pesquisador, produziu esta obra clássica. Aqui são compilados 130 casos de variadas origens, cuidadosamente ordenados por sua natureza, entre casos telepáticos em que os animais são não apenas receptores mas também emissores; casos de animais que percebem, juntamente com pessoas, espíritos e outras manifestações supranormais, incluindo os que ocorrem em lugares ditos assombrados; e ainda os casos notáveis de materializações de animais mortos, inclusive muito tempo atrás, em sessões experimentais com médiuns renomados; finalizando com aparições de animais desencarnados identificados. Muitos são oriundos de obras de consagrados autores da área parapsíquica. Com a precisão metodológica e a visão científica que o caracteriza, Bozzano oferece notável subsídio para trazer ao conhecimento geral os fatos que apontam de forma inequívoca para uma conclusão: os animais têm alma, sobrevivem à morte do corpo, revestem-se de um perispírito que pode se fazer visível e materializar-se, e são dotados de faculdades psíquicas que se revelam em fenômenos supranormais diversos. É o que ressalta desta obra clássica, pioneira nessa temática fundamental para nossa compreensão do processo evolutivo como um todo.','media/livraria/capas/cover_20260324142635_cf26891e.jpg','image/jpeg',116907,29.26,47.00,2,1,'active','Prateleira 3','2026-03-24 11:28:33','2026-03-24 14:26:36'),(114,'MAGNETISMO','magnetismo',1,'Estudo (Intermediário)',11,'Doutrinário',12,'Magnetismo','A ARTE DE MAGNETIZAR OU, MAGNETISMO ANIMAL',NULL,'Jacob Melo','VIDA E SABER','978-85-7924-710-1','978-8579247101','2ª Edição',1,NULL,2020,NULL,'Português','A leitura desta obra tornará seus conhecimentos muito mais ricos do que você poderia imaginar. O grande magnetizador Charles Lafontaine agora integra nossa série clássicos do magnetismo, trazendo-nos uma vasta experiência com curas de surdos, mudos,cegos, paraliticos, portadores de tumores, cânceres e uma imensidão de problemas, muitos dos quais não tinham sido trabalhados eficientemente, até que ele apresentou seus procedimentos. Mesmo tendo sido publicado á mais de 160 anos, suas abordagens são atuais e pedem atenção á todo aquele que pretenda magnetizar com segurança e eficiência. Charles Lafontaine aqui está, desfrute de seu estilo e de sua experiência.','media/livraria/capas/cover_20260324142448_5952a50b.jpg','image/jpeg',154751,0.01,40.00,3,1,'active','Prateleira 6','2026-03-24 11:45:49','2026-03-24 14:24:48'),(115,'A ARTE DE RECOMEçAR','arte-de-recome-ar',7,'Espiritualismo  e Filosofia Espiritual',1,'Filosofia espiritual',8,'Consolação e Esperança','A Arte de Recomeçar',NULL,'Cirinéia Iolanda Maffei','Boa Nova','8583530416','978-8583530411','Padão',1,NULL,2008,NULL,'Português','De onde vem? Para onde vamos? Por que estamos a sobre a Terra? Vivemos realmente muitas existências? Assim sendo quem fomos no Pretérito? Reis, rainhas, cortesãs, plebeus, sacerdotes, soldados, senhores ou escravos? Onde nascemos? Quais os amores em nossos destinos e onde estarão hoje? Poderemos reencontrá-los, reconhecendo-os? Em \'Arte de Recomeçar\', o autor espiritual Tolstoi, Léon mais uma vez recorre a textos bíblicos da época de Jesus, ao \'O Evangelho Segundo o Espiritismo\' e as belíssimas narrativas de pessoas anônimas, muito parecidas conosco, enfocando, de maneira esclarecedora e envolvente, o tema reencarnação. Mergulhados em suas páginas, realizaremos uma viagem ao passado de mais de dois mil anos, identificando-nos com os personagens, reconhecendo-nos em seus sentimentos, intuindo havermos trilhado caminhos semelhantes, dos quais guardamos tênues reminiscências, inexplicáveis emoções, imprecisas saudades...','media/livraria/capas/cover_20260324115611_579c4a62.jpg','image/jpeg',52277,36.40,58.00,2,1,'active','Prateleira 2','2026-03-24 11:56:11','2026-03-24 11:56:40'),(116,'A ARTE DE SEGUIR EM FRENTE','arte-de-seguir',7,'Espiritualismo  e Filosofia Espiritual',1,'Filosofia espiritual',10,'Motivação e Espiritualidade','A ARTE DE SEGUIR EM FRENTE',NULL,'André Trigueiro','Intervidas','8560960457','978-8560960453','1ª Edição',1,NULL,2025,NULL,'Português','André Trigueiro apresenta 100 mensagens breves, precisas e impactantes para enfrentar desafios vivenciais. O autor demonstra que a vida não é obra do acaso, um acidente de percurso, algo desprovido de um sentido mais profundo que nos alcança e nos conecta com tudo o que vai à volta. O livro chama a atenção para os pequenos sinais, as pistas que cada momento da existência pode nos trazer em situações inusitadas, encontros inesperados, e tudo o que puder nos ajudar a avançar. Fique ligado e descubra a arte de seguir em frente!','media/livraria/capas/cover_20260324120538_4374a45f.jpg','image/jpeg',67761,18.50,29.90,2,1,'active','Expositor Balcão','2026-03-24 12:05:38','2026-03-24 12:05:38'),(117,'A ARTE DO REENCONTRO','arte-do-reencontro',7,'Espiritualismo  e Filosofia Espiritual',10,'Literatura espiritualista',8,'Consolação e Esperança','A ARTE DO REENCONTRO',NULL,'Alberto Almeida','FEP','8586255424','978-8586255427','1ª Edição',1,NULL,2020,NULL,'Português','Dividida em 21 capítulos, esta obra é um convite à reflexão sobre a dinâmica do relacionamento a dois, e sobre os elementos que impactam a saúde da relação de conjugalidade. Temas como casamento e filhos de outro casamento; casamento e infidelidadeconjugal; casamento e perdão; casamento e separação; casamento e sogra; delimitações de papeis entre conjugues e filhos são abordados nesta obra que oferece, inclusive, uma proposta de três modelos de casamento. Trata-se de um livro que deve, além detudo, ser utilizado por grupos de estudos e reflexões.','media/livraria/capas/cover_20260324144020_aa0921ea.jpg','image/jpeg',12358,0.01,35.00,1,1,'active','Prateleira 3','2026-03-24 12:12:46','2026-03-24 14:40:20'),(118,'A BIOLOGIA DA CRENçA','biologia-da-cren-a',14,'Espiritismo Experimental',1,'Filosofia espiritual',2,'Saúde','A Biologia da Crença',NULL,'Bruce H. Lipton','Butterfly','858847767X','ISBN-13 978-8588477674','19ª Edição',1,NULL,2007,NULL,'Português','Este livro vai mudar sua vida: novas e surpreendentes descobertas científicas demonstram que as células do corpo são influenciadas pelo nosso pensamento e ajudam a comprovar a reencarnação. O cientista Bruce Lipton, renomado biólogo norte-americano,descreve as reações químicas do processo celular e comprova cientificamente suas descobertas que revolucionaram a Biologia. Best-seller nos Estados Unidos, \"A Biologia da Crença\" é um livro ilustrado, escrito em linguagem simples e direta, repleto deexemplos que demonstram, na prática, como a Nova Biologia está mudando o modo de pensar de milhares de pessoas em todo mundo.','media/livraria/capas/cover_20260324123023_9103e818.jpg','image/jpeg',32488,26.80,49.90,1,1,'active','Prateleira 6','2026-03-24 12:30:23','2026-03-24 12:30:23');
/*!40000 ALTER TABLE `bookshop_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_sales`
--

DROP TABLE IF EXISTS `bookshop_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sold_at` datetime NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_count` int unsigned NOT NULL DEFAULT '0',
  `subtotal_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `received_amount` decimal(10,2) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_by_member_id` bigint unsigned DEFAULT NULL,
  `created_by_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by_member_id` bigint unsigned DEFAULT NULL,
  `cancelled_by_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sale_code` (`sale_code`),
  KEY `idx_bookshop_sales_sold_at` (`sold_at`),
  KEY `idx_bookshop_sales_status` (`status`),
  KEY `idx_bookshop_sales_payment` (`payment_method`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_sales`
--

LOCK TABLES `bookshop_sales` WRITE;
/*!40000 ALTER TABLE `bookshop_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookshop_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_sale_items`
--

DROP TABLE IF EXISTS `bookshop_sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_sale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `book_id` bigint unsigned NOT NULL,
  `stock_lot_id` bigint unsigned DEFAULT NULL,
  `stock_lot_code_snapshot` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_snapshot` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_cost_snapshot` decimal(10,2) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `line_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bookshop_sale_items_sale` (`sale_id`),
  KEY `idx_bookshop_sale_items_book` (`book_id`),
  KEY `idx_bookshop_sale_items_lot` (`stock_lot_id`),
  CONSTRAINT `fk_bookshop_sale_items_book` FOREIGN KEY (`book_id`) REFERENCES `bookshop_books` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookshop_sale_items_sale` FOREIGN KEY (`sale_id`) REFERENCES `bookshop_sales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_sale_items`
--

LOCK TABLES `bookshop_sale_items` WRITE;
/*!40000 ALTER TABLE `bookshop_sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookshop_sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_stock_lots`
--

DROP TABLE IF EXISTS `bookshop_stock_lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_stock_lots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `book_id` bigint unsigned NOT NULL,
  `source_movement_id` bigint unsigned DEFAULT NULL,
  `quantity_received` int unsigned NOT NULL DEFAULT '1',
  `quantity_available` int unsigned NOT NULL DEFAULT '1',
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `unit_sale_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `occurred_at` datetime NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bookshop_stock_lots_book` (`book_id`),
  KEY `idx_bookshop_stock_lots_movement` (`source_movement_id`),
  KEY `idx_bookshop_stock_lots_available` (`quantity_available`),
  KEY `idx_bookshop_stock_lots_occurred_at` (`occurred_at`),
  CONSTRAINT `fk_bookshop_stock_lots_book` FOREIGN KEY (`book_id`) REFERENCES `bookshop_books` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_stock_lots`
--

LOCK TABLES `bookshop_stock_lots` WRITE;
/*!40000 ALTER TABLE `bookshop_stock_lots` DISABLE KEYS */;
INSERT INTO `bookshop_stock_lots` VALUES (53,66,NULL,10,10,25.25,43.68,'2026-03-23 00:33:03','Lote inicial gerado automaticamente para compatibilidade do estoque.','2026-03-23 23:42:33'),(58,72,NULL,17,17,39.35,63.35,'2026-03-22 21:12:16','Lote inicial gerado automaticamente para compatibilidade do estoque.','2026-03-23 23:42:33'),(85,66,NULL,9,9,0.10,35.00,'2026-03-24 14:33:42','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:25'),(86,112,NULL,1,1,0.01,30.00,'2026-03-24 14:30:10','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:25'),(87,113,NULL,2,2,29.26,47.00,'2026-03-24 14:26:36','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:25'),(88,114,NULL,3,3,0.01,40.00,'2026-03-24 14:24:48','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:25'),(89,115,NULL,2,2,36.40,58.00,'2026-03-24 11:56:40','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:25'),(90,116,NULL,2,2,18.50,29.90,'2026-03-24 12:05:38','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:26'),(91,117,NULL,1,1,0.01,35.00,'2026-03-24 14:40:20','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:26'),(92,118,NULL,1,1,26.80,49.90,'2026-03-24 12:30:23','Lote de saldo inicial gerado automaticamente para compatibilidade do estoque.','2026-03-24 14:41:26');
/*!40000 ALTER TABLE `bookshop_stock_lots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookshop_stock_movements`
--

DROP TABLE IF EXISTS `bookshop_stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshop_stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `book_id` bigint unsigned NOT NULL,
  `sku_snapshot` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movement_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `stock_delta` int NOT NULL DEFAULT '0',
  `stock_before` int NOT NULL DEFAULT '0',
  `stock_after` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `unit_sale_price` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `total_sale_value` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `occurred_at` datetime NOT NULL,
  `created_by_member_id` bigint unsigned DEFAULT NULL,
  `created_by_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bookshop_stock_movements_book` (`book_id`),
  KEY `idx_bookshop_stock_movements_type` (`movement_type`),
  KEY `idx_bookshop_stock_movements_occurred_at` (`occurred_at`),
  KEY `idx_bookshop_stock_movements_created_by` (`created_by_name`),
  CONSTRAINT `fk_bookshop_stock_movements_book` FOREIGN KEY (`book_id`) REFERENCES `bookshop_books` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookshop_stock_movements`
--

LOCK TABLES `bookshop_stock_movements` WRITE;
/*!40000 ALTER TABLE `bookshop_stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookshop_stock_movements` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-24 16:21:00
