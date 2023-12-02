-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Set 18, 2023 alle 16:07
-- Versione del server: 10.4.24-MariaDB
-- Versione PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `anagrafica`
--

CREATE TABLE `anagrafica` (
  `id` int(5) NOT NULL,
  `codcliente` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cognome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `piva` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cf` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cd` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cell` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fax` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pec` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `www` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indirizzo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cap` int(10) DEFAULT NULL,
  `citta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pv` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pve` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stato` int(3) DEFAULT NULL,
  `privato` int(1) NOT NULL DEFAULT 0,
  `tipo` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'c'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `anagrafica`
--

INSERT INTO `anagrafica` (`id`, `codcliente`, `nome`, `cognome`, `rs`, `piva`, `cf`, `cd`, `tel`, `cell`, `fax`, `email`, `pec`, `www`, `indirizzo`, `cap`, `citta`, `pv`, `pve`, `stato`, `privato`, `tipo`) VALUES
(1, 'QKPPCV', 'Michele', 'Pittia', 'tt', '', '', '', '', '33', '', 'meltit72@gmail.com', '', '', 'Via Premariacco 69', 33043, 'Cividale del Friuli', 'Ci', 'UD', 102, 1, 'c');

-- --------------------------------------------------------

--
-- Struttura della tabella `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `tipo_config` varchar(50) DEFAULT NULL,
  `parametro_config` varchar(50) DEFAULT NULL,
  `valore_config` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `config`
--

INSERT INTO `config` (`id`, `tipo_config`, `parametro_config`, `valore_config`) VALUES
(1, 'email', 'host', 'smtps.aruba.it'),
(2, 'email', 'port', '465'),
(3, 'email', 'username', 'assistenza@win-service.biz'),
(4, 'email', 'password', 'Ass1stenz@'),
(5, 'email', 'from', 'assistenza@win-service.biz'),
(6, 'email', 'replayto', 'meltit72@gmail.com'),
(7, 'email', 'fromname', 'Prova'),
(8, 'email', 'replaytoname', 'Prova');

-- --------------------------------------------------------

--
-- Struttura della tabella `stati`
--

CREATE TABLE `stati` (
  `id_stati` int(16) NOT NULL,
  `nome_stati` varchar(128) DEFAULT NULL,
  `sigla_stati` varchar(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `stati`
--

INSERT INTO `stati` (`id_stati`, `nome_stati`, `sigla_stati`) VALUES
(1, 'Afghanistan', 'AF'),
(2, 'Albania', 'AL'),
(3, 'Algeria', 'DZ'),
(4, 'Andorra', 'AD'),
(5, 'Angola', 'AO'),
(6, 'Anguilla', 'AI'),
(7, 'Antartide', 'AQ'),
(8, 'Antigua e Barbuda', 'AG'),
(9, 'Arabia Saudita', 'SA'),
(10, 'Argentina', 'AR'),
(11, 'Armenia', 'AM'),
(12, 'Aruba', 'AW'),
(13, 'Australia', 'AU'),
(14, 'Austria', 'AT'),
(15, 'Azerbaigian', 'AZ'),
(16, 'Bahamas', 'BS'),
(17, 'Bahrein', 'BH'),
(18, 'Bangladesh', 'BD'),
(19, 'Barbados', 'BB'),
(20, 'Belgio', 'BE'),
(21, 'Belize', 'BZ'),
(22, 'Benin', 'BJ'),
(23, 'Bermuda', 'BM'),
(24, 'Bhutan', 'BT'),
(25, 'Bielorussia', 'BY'),
(26, 'Birmania', 'MM'),
(27, 'Bolivia', 'BO'),
(28, 'Bosnia ed Erzegovina', 'BA'),
(29, 'Botswana', 'BW'),
(30, 'Brasile', 'BR'),
(31, 'Brunei', 'BN'),
(32, 'Bulgaria', 'BG'),
(33, 'Burkina Faso', 'BF'),
(34, 'Burundi', 'BI'),
(35, 'Cambogia', 'KH'),
(36, 'Camerun', 'CM'),
(37, 'Canada', 'CA'),
(38, 'Capo Verde', 'CV'),
(39, 'Ciad', 'TD'),
(40, 'Cile', 'CL'),
(41, 'Cina', 'CN'),
(42, 'Cipro', 'CY'),
(43, 'Citt', 'VA'),
(44, 'Colombia', 'CO'),
(45, 'Comore', 'KM'),
(46, 'Corea del Nord', 'KP'),
(47, 'Corea del Sud', 'KR'),
(48, 'Costa d\'Avorio', 'CI'),
(49, 'Costa Rica', 'CR'),
(50, 'Croazia', 'HR'),
(51, 'Cuba', 'CU'),
(52, 'Cura', 'CW'),
(53, 'Danimarca', 'DK'),
(54, 'Dominica', 'DM'),
(55, 'Ecuador', 'EC'),
(56, 'Egitto', 'EG'),
(57, 'El Salvador', 'SV'),
(58, 'Emirati Arabi Uniti', 'AE'),
(59, 'Eritrea', 'ER'),
(60, 'Estonia', 'EE'),
(61, 'Etiopia', 'ET'),
(62, 'Figi', 'FJ'),
(63, 'Filippine', 'PH'),
(64, 'Finlandia', 'FI'),
(65, 'Francia', 'FR'),
(66, 'Gabon', 'GA'),
(67, 'Gambia', 'GM'),
(68, 'Georgia', 'GE'),
(69, 'Georgia del Sud e isole Sandwich meridionali', 'GS'),
(70, 'Germania', 'DE'),
(71, 'Ghana', 'GH'),
(72, 'Giamaica', 'JM'),
(73, 'Giappone', 'JP'),
(74, 'Gibilterra', 'GI'),
(75, 'Gibuti', 'DJ'),
(76, 'Giordania', 'JO'),
(77, 'Grecia', 'GR'),
(78, 'Grenada', 'GD'),
(79, 'Groenlandia', 'GL'),
(80, 'Guadalupa', 'GP'),
(81, 'Guam', 'GU'),
(82, 'Guatemala', 'GT'),
(83, 'Guernsey', 'GG'),
(84, 'Guinea', 'GN'),
(85, 'Guinea-Bissau', 'GW'),
(86, 'Guinea Equatoriale', 'GQ'),
(87, 'Guyana', 'GY'),
(88, 'Guyana francese', 'GF'),
(89, 'Haiti', 'HT'),
(90, 'Honduras', 'HN'),
(91, 'Hong Kong', 'HK'),
(92, 'India', 'IN'),
(93, 'Indonesia', 'ID'),
(94, 'Iran', 'IR'),
(95, 'Iraq', 'IQ'),
(96, 'Irlanda', 'IE'),
(97, 'Islanda', 'IS'),
(98, 'Isola Bouvet', 'BV'),
(99, 'Isola di Man', 'IM'),
(100, 'Isola di Natale', 'CX'),
(101, 'Isola Norfolk', 'NF'),
(102, 'Isole ', 'AX'),
(103, 'Isole BES', 'BQ'),
(104, 'Isole Cayman', 'KY'),
(105, 'Isole Cocos (Keeling)', 'CC'),
(106, 'Isole Cook', 'CK'),
(107, 'F', 'FO'),
(108, 'Isole Falkland', 'FK'),
(109, 'Isole Heard e McDonald', 'HM'),
(110, 'Isole Marianne Settentrionali', 'MP'),
(111, 'Isole Marshall', 'MH'),
(112, 'Isole minori esterne degli Stati Uniti', 'UM'),
(113, 'Isole Pitcairn', 'PN'),
(114, 'Isole Salomone', 'SB'),
(115, 'Isole Vergini britanniche', 'VG'),
(116, 'Isole Vergini americane', 'VI'),
(117, 'Israele', 'IL'),
(118, 'Italia', 'IT'),
(119, 'Jersey', 'JE'),
(120, 'Kazakistan', 'KZ'),
(121, 'Kenya', 'KE'),
(122, 'Kirghizistan', 'KG'),
(123, 'Kiribati', 'KI'),
(124, 'Kuwait', 'KW'),
(125, 'Laos', 'LA'),
(126, 'Lesotho', 'LS'),
(127, 'Lettonia', 'LV'),
(128, 'Libano', 'LB'),
(129, 'Liberia', 'LR'),
(130, 'Libia', 'LY'),
(131, 'Liechtenstein', 'LI'),
(132, 'Lituania', 'LT'),
(133, 'Lussemburgo', 'LU'),
(134, 'Macao', 'MO'),
(135, 'Macedonia', 'MK'),
(136, 'Madagascar', 'MG'),
(137, 'Malawi', 'MW'),
(138, 'Malesia', 'MY'),
(139, 'Maldive', 'MV'),
(140, 'Mali', 'ML'),
(141, 'Malta', 'MT'),
(142, 'Marocco', 'MA'),
(143, 'Martinica', 'MQ'),
(144, 'Mauritania', 'MR'),
(145, 'Mauritius', 'MU'),
(146, 'Mayotte', 'YT'),
(147, 'Messico', 'MX'),
(148, 'Micronesia', 'FM'),
(149, 'Moldavia', 'MD'),
(150, 'Mongolia', 'MN'),
(151, 'Montenegro', 'ME'),
(152, 'Montserrat', 'MS'),
(153, 'Mozambico', 'MZ'),
(154, 'Namibia', 'NA'),
(155, 'Nauru', 'NR'),
(156, 'Nepal', 'NP'),
(157, 'Nicaragua', 'NI'),
(158, 'Niger', 'NE'),
(159, 'Nigeria', 'NG'),
(160, 'Niue', 'NU'),
(161, 'Norvegia', 'NO'),
(162, 'Nuova Caledonia', 'NC'),
(163, 'Nuova Zelanda', 'NZ'),
(164, 'Oman', 'OM'),
(165, 'Paesi Bassi', 'NL'),
(166, 'Pakistan', 'PK'),
(167, 'Palau', 'PW'),
(168, 'Palestina', 'PS'),
(169, 'Panam', 'PA'),
(170, 'Papua Nuova Guinea', 'PG'),
(171, 'Paraguay', 'PY'),
(172, 'Per', 'PE'),
(173, 'Polinesia Francese', 'PF'),
(174, 'Polonia', 'PL'),
(175, 'Porto Rico', 'PR'),
(176, 'Portogallo', 'PT'),
(177, 'Monaco', 'MC'),
(178, 'Qatar', 'QA'),
(179, 'Regno Unito', 'GB'),
(180, 'RD del Congo', 'CD'),
(181, 'Rep. Ceca', 'CZ'),
(182, 'Rep. Centrafricana', 'CF'),
(183, 'Rep. del Congo', 'CG'),
(184, 'Rep. Dominicana', 'DO'),
(185, 'Riunione', 'RE'),
(186, 'Romania', 'RO'),
(187, 'Ruanda', 'RW'),
(188, 'Russia', 'RU'),
(189, 'Sahara Occidentale', 'EH'),
(190, 'Saint Kitts e Nevis', 'KN'),
(191, 'Santa Lucia', 'LC'),
(192, 'Sant\'Elena, Ascensione e Tristan da Cunha', 'SH'),
(193, 'Saint Vincent e Grenadine', 'VC'),
(194, 'Saint-Barth', 'BL'),
(195, 'Saint-Martin', 'MF'),
(196, 'Saint-Pierre e Miquelon', 'PM'),
(197, 'Samoa', 'WS'),
(198, 'Samoa Americane', 'AS'),
(199, 'San Marino', 'SM'),
(200, 'S', 'ST'),
(201, 'Senegal', 'SN'),
(202, 'Serbia', 'RS'),
(203, 'Seychelles', 'SC'),
(204, 'Sierra Leone', 'SL'),
(205, 'Singapore', 'SG'),
(206, 'Sint Maarten', 'SX'),
(207, 'Siria', 'SY'),
(208, 'Slovacchia', 'SK'),
(209, 'Slovenia', 'SI'),
(210, 'Somalia', 'SO'),
(211, 'Spagna', 'ES'),
(212, 'Sri Lanka', 'LK'),
(213, 'Stati Uniti', 'US'),
(214, 'Sudafrica', 'ZA'),
(215, 'Sudan', 'SD'),
(216, 'Sudan del Sud', 'SS'),
(217, 'Suriname', 'SR'),
(218, 'Svalbard e Jan Mayen', 'SJ'),
(219, 'Svezia', 'SE'),
(220, 'Svizzera', 'CH'),
(221, 'Swaziland', 'SZ'),
(222, 'Taiwan', 'TW'),
(223, 'Tagikistan', 'TJ'),
(224, 'Tanzania', 'TZ'),
(225, 'Terre australi e antartiche francesi', 'TF'),
(226, 'Territorio britannico dell\'oceano Indiano', 'IO'),
(227, 'Thailandia', 'TH'),
(228, 'Timor Est', 'TL'),
(229, 'Togo', 'TG'),
(230, 'Tokelau', 'TK'),
(231, 'Tonga', 'TO'),
(232, 'Trinidad e Tobago', 'TT'),
(233, 'Tunisia', 'TN'),
(234, 'Turchia', 'TR'),
(235, 'Turkmenistan', 'TM'),
(236, 'Turks e Caicos', 'TC'),
(237, 'Tuvalu', 'TV'),
(238, 'Ucraina', 'UA'),
(239, 'Uganda', 'UG'),
(240, 'Ungheria', 'HU'),
(241, 'Uruguay', 'UY'),
(242, 'Uzbekistan', 'UZ'),
(243, 'Vanuatu', 'VU'),
(244, 'Venezuela', 'VE'),
(245, 'Vietnam', 'VN'),
(246, 'Wallis e Futuna', 'WF'),
(247, 'Yemen', 'YE'),
(248, 'Zambia', 'ZM'),
(249, 'Zimbabwe', 'ZW');

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `id_user` int(2) NOT NULL,
  `nome` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cognome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imm_profilo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lingua_user` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'it',
  `controllo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruolo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `act` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id_user`, `nome`, `cognome`, `username`, `password`, `imm_profilo`, `lingua_user`, `controllo`, `ruolo`, `act`) VALUES
(8, 'Michele', 'Pittia', 'meltit72@gmail.com', '9eca34f8b685476f415bf2fa20f99a0c', '19bmx8tr.jpg', 'it', '7iayidft', 'sadmin', 1),
(20, 'Susan', 'Chandradihardja', 'sioesan74@gmail.com', '', '353k5hg8.jpg', 'it', 'vlmf5br8', 'segr', 1),
(23, 'Michele', 'Pittia', 'meltit75@gmail.com', '', 'iycsbm5m.jpg', 'it', '4j1qkmah', 'admin', 2);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `anagrafica`
--
ALTER TABLE `anagrafica`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `stati`
--
ALTER TABLE `stati`
  ADD PRIMARY KEY (`id_stati`),
  ADD KEY `PRIMARY_KEY` (`id_stati`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `anagrafica`
--
ALTER TABLE `anagrafica`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `stati`
--
ALTER TABLE `stati`
  MODIFY `id_stati` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
