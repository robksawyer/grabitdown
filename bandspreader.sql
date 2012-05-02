-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 02, 2012 at 01:33 AM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bandspreader`
--

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` VALUES(1, 3, 'zv9g3tikcw2rj7pebdf8uo5xy', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(2, 3, 'b3i0ug9apk2hfrsxn7vwyoczl', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(3, 3, 'lkh4w8j37s2mgnb6zqerfud0c', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(4, 3, 'tuv9cyeao0sml7nkqrfp48hxw', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(5, 3, 'hd6mfe7qyjk528g01t9wxnuvp', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(6, 3, '4bu80ki9l56xnjvcegotm2hsw', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(7, 3, 'btvw3em5sihp87xucjy16zo9n', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(8, 3, '9nrbi63wjl7xa18fuk5mghdv2', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(9, 3, 'srdhja6m8uwb0qgvekf24yl3z', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(10, 3, 'zunbxp7jht2ovadic46keqglr', 1, '2012-05-02 01:02:14', '2012-05-02 01:02:14');
INSERT INTO `codes` VALUES(11, 5, 'mrbygqcn1f8vh5j40leotw3uk', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(12, 5, 'h1qeu0sb3ontwrmzpal7v46j9', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(13, 5, '2iq3hz6covbua0wf4lk9ygp8d', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(14, 5, 'e7ujt9wd5y2omxfs4aqgpr01k', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(15, 5, 'zjtsvnihl654wyaeu10qrpf2g', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(16, 5, 'g65c0ojpfntdqsm3xyiw4kvur', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(17, 5, 'dnsouhybegq7r69a4j00pcfli', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(18, 5, 'rok053bqiz12af9e8umct76gy', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(19, 5, 'udo5yf9wem6zvsk0cgl8rbn2i', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(20, 5, 'g3f4vdicej08q9yls1apz6wno', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(21, 5, 'qtuc97obamw02gipez4ls51rx', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(22, 5, 'itp9632gkrxjszvc15dywohna', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(23, 5, 'sfi5vk9wtp0z3gb2a7e4cj1lh', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(24, 5, 'u6pg097lf4ehvwmit5sr3j2c1', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(25, 5, '4xhoavfeyqsp531rz7lu2kmc9', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(26, 5, 'ebty5z40p7mxworl8jnq1269c', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(27, 5, 'scpyaqw9libk35r012exdtoh7', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(28, 5, 'done8sljtvgw50xqpik4fac3y', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(29, 5, 'iz8aou9gh4jks71t2lwcnrbf3', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(30, 5, 'kf0dyswtz2uobia9l183egrjv', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(31, 5, '84bo1eiygj9w6kthm0vd7nxsc', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(32, 5, '3n9w6dpj85byhmv7kzse40gql', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(33, 5, 'v90mxgc5ri4qtfon63kehd78b', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(34, 5, '8wb9ftvoiun67xap4qrm31clg', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(35, 5, 'mdgitf7un4zyo8ha2r59klvw1', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(36, 5, 'exypo7ds31wjkf2t54lgmhzr0', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(37, 5, 'qmgvw3hbyx9nr4du561zct2if', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(38, 5, 'n1hgjzife96ct5adu83blpy7s', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(39, 5, '57eho8tys0px6k2b9gjfnwd1q', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(40, 5, '9sg87rfde3qy1oz6j42wlav0h', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(41, 5, 'mw6sdq5aje8gl1u74c39fpxvy', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(42, 5, 'm0jw9b7cg1ts5hznvqdf4yrai', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(43, 5, 'fb1oxpq92ghiwcsy0aj6evrdl', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(44, 5, 'n8ilz47f01spxudymcg3v6wqt', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(45, 5, 'dy7njg21f3loxcbevs6kptha9', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(46, 5, 'yitw9rgm24d7s5p3anjcz81lq', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(47, 5, '4gkdht587x1qfcmn2ypo9viab', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(48, 5, 'y4u6bslvq72nm5xk1o00cdatg', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(49, 5, '7h49z8uoeqvksnpyj23w6i5x0', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(50, 5, 'q69pnzohudf2cvt40e3sjmx7r', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(51, 5, '2le8np5dqxomu1wirbkhsf9ty', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(52, 5, 'ih2nt8z3beyjcm69aopflrxgq', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(53, 5, 'jr0fwi1en4hv7udxg25qtlz6b', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(54, 5, 'gp5eomcaxjqfzbk238s0wdh6i', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(55, 5, '8rislxocpb2d0tjf713hv6mwn', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(56, 5, 's6yptabn1go9ei284wh5rfukd', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(57, 5, '3i84es20fovj9ak6mxcqyuztn', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(58, 5, 'bdt9g3yj0usf7piamev1c4ohz', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(59, 5, 'b03i5f28a6rdjhu7mzqsntlgk', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(60, 5, '9yg1mpr4lc2t8dxsbqfa5k6u3', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(61, 5, 'ex9qgk05par4612mnfohtyvbd', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(62, 5, '1nzwtmg8bdfx42esqc6ia07kv', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(63, 5, 'f8bznjxsre0vkh2d4763c5u9t', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(64, 5, '86aoju2s71bdehktym09riw4v', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(65, 5, 'awbrxio03u26kvlsc51ehygz7', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(66, 5, '400ni6lygwqc25jm9thvf7ads', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(67, 5, '0y5ual4qvps8i1eo9kgdt3wz6', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(68, 5, 'o4wmnid7ebf5a0xtuqlj9hycr', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(69, 5, 'r4dnmtqc69wxph01avgy8zo5e', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(70, 5, 'fhprv53nym8cglkjti6o9uxed', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(71, 5, 'ugkqz24y3pd9lrm5vobcietwh', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(72, 5, 'dsfjq972pz3nkvgih8ly01xbr', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(73, 5, 'dgbpkw7ix1n290uf6rmo4t8sj', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(74, 5, 'st94el5chbqxo3gnafivudmyr', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(75, 5, '1pazdrks2lt34wn95hb7oj68f', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(76, 5, 'iun0xd65pqk4rm1wybftgsol9', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(77, 5, 'zd5qhtwlcjs1uf4y0o36ip2rg', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(78, 5, 'vo8l5xz3yg1mfhkunwj6eqcri', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(79, 5, '08opmwcj9vgh6ibkqxfus37yd', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(80, 5, 'c6wz52s1e9ayf7md3pxtgn0br', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(81, 5, '5lq4zr9bj8skndo7mtxe0wvhc', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(82, 5, 'ops4x81qndmvkwcjl26u0fie7', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(83, 5, 'qumarytbpi5dczvjfs92nl8wk', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(84, 5, 'odiy2z89chkx6tbjaw0v4p13l', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(85, 5, '38gnbeirwchmulaqyoz7k01px', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(86, 5, 'nzemdlp62vtf0aiuxk38b174h', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(87, 5, 'emhp9w2ay87qjlkd54tsbngi6', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(88, 5, '1vuyedw62shjabnf54gtprqz0', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(89, 5, 'wuo79ipx3dsag8h5mne0rzl1j', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(90, 5, 'v5bwgd6ormi428ucxs19kplzt', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(91, 5, 'cnlxsv9taqo83pj7ui5z61wbf', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(92, 5, 'c2ya4qk9xemdznut5j3h7gwv1', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(93, 5, 'sga1jhlowre2nv95cxfiubmz4', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(94, 5, 'uwbp6rzft1xi034vqomed8g75', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(95, 5, 'wlnvkd06a49sx71mg5yt8qe2r', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(96, 5, 'inmlg5hue38t2jdbf00c9kv4z', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(97, 5, 'na1hfpuq2cljyxribd7540vwo', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(98, 5, 'jxpozml65c0u8sefgyrqvkba2', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(99, 5, 'y2uqxj15sdnzt3cpfebkhi8rl', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(100, 5, 'kn8mvrbg3x97ulwq1sayf6th4', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(101, 5, 'pjiynog8wq61l2rc7e4bsfam5', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(102, 5, 'bgcjafxsrn27umh6p1t93kz4q', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(103, 5, '87hvtq46yobfxe9w5ikpmu3cz', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(104, 5, 's834vy7cqghteuf61rmzapin9', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(105, 5, '1ld2hcbspqzienawtg38m970y', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(106, 5, 'jy7p54vc0xgi3roustew9d8zm', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(107, 5, 'vrqlao598hwg1ktc07emysu3p', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(108, 5, 'yk9xb6er0vmsjofgpw3z2dh5c', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(109, 5, 'snmjbvkf4ziuqxcl0hor586pe', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');
INSERT INTO `codes` VALUES(110, 5, 'ga0ot3d4q9b8pe6hjmcnkf1lr', 1, '2012-05-02 08:28:48', '2012-05-02 08:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `path_alt` varchar(255) DEFAULT NULL,
  `caption` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `filesize` varchar(255) NOT NULL,
  `ext` varchar(10) NOT NULL,
  `group` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `user_id` char(36) NOT NULL,
  `total_codes` int(11) NOT NULL,
  `test_token` varchar(255) NOT NULL,
  `test_token_count` int(10) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` VALUES(3, '09 Brooklyn In My Mind (feat. Mos Def, Jean Grae, & Memphis Bleek).mp3', '/files/uploads/robksawyer/09_Brooklyn_In_My_Mind_feat._Mos_Def_Jean_Grae__Memphis_Bleek.mp3', NULL, '09 Brooklyn In My Mind (feat. Mos Def, Jean Grae, & Memphis Bleek).mp3', 'audio/mp3', 4728139, '5 MB', 'mp3', 'audio', '09_brooklyn_in_my_mind_feat_mos_def_jean_grae_memphis_bleek_mp3', '4fa07bda-f5bc-4377-8d50-14a2bd22bb5a', 10, '461d171369e37d2c89d08f4c823ff918', 0, 1, '2012-05-02 00:12:10', '2012-05-02 00:53:31', '2012-05-02 00:12:10');
INSERT INTO `uploads` VALUES(4, '14 Shots (feat. DHO & Sean Price).mp3', '/files/uploads/robksawyer2/14_Shots_feat._DHO__Sean_Price.mp3', NULL, '14 Shots (feat. DHO & Sean Price).mp3', 'audio/mp3', 3904990, '4 MB', 'mp3', 'audio', '14_shots_feat_dho_sean_price_mp3', '4fa0ee2c-94fc-4f16-878f-06e9bd22bb5a', 0, '49363cac3ad8301b18ba16f750c07e3d', 0, 0, '2012-05-02 08:19:57', '2012-05-02 08:19:57', '2012-05-02 08:19:57');
INSERT INTO `uploads` VALUES(5, 'The Hardest Thing.mp3', '/files/uploads/test/The_Hardest_Thing.mp3', NULL, 'The Hardest Thing.mp3', 'audio/mp3', 4410845, '4 MB', 'mp3', 'audio', 'the_hardest_thing_mp3', '4fa0f01c-4c70-433c-8c90-06d3bd22bb5a', 100, '3b955b9709624fc39fe41a345bb199ca', 0, 1, '2012-05-02 08:28:12', '2012-05-02 08:28:12', '2012-05-02 08:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL COMMENT 'uuid',
  `fullname` varchar(255) NOT NULL,
  `custom_path` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `passwd` varchar(128) DEFAULT NULL,
  `password_token` varchar(128) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_authenticated` tinyint(1) NOT NULL DEFAULT '0',
  `email_token` varchar(255) DEFAULT NULL,
  `email_token_expires` datetime DEFAULT NULL,
  `tos` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `role` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `username` (`custom_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES('4fa07bda-f5bc-4377-8d50-14a2bd22bb5a', '', 'robksawyer', 'robksawyer', '5fac330e269584886470fea530d2db42635cc6bc', NULL, 'robksawyer@gmail.com', 0, 'knd9m5go0i', '2012-05-03 00:12:10', 1, 1, NULL, NULL, 0, 'user', '2012-05-02 00:12:10', '2012-05-02 00:12:10');
INSERT INTO `users` VALUES('4fa0ee2c-94fc-4f16-878f-06e9bd22bb5a', '', 'robksawyer2', 'robksawyer2', 'efaec3bfea39c425b25a251ffb181dfd11d334f7', NULL, 'robksawyer+test@gmail.com', 0, 'yam45wn9lx', '2012-05-03 08:19:56', 1, 1, NULL, NULL, 0, 'user', '2012-05-02 08:19:56', '2012-05-02 08:19:56');
INSERT INTO `users` VALUES('4fa0f01c-4c70-433c-8c90-06d3bd22bb5a', '', 'test', 'test', '3bf380cf6195aa829470fab350348ed233d1518b', NULL, 'test@test.com', 0, 'dbk62q3ylz', '2012-05-03 08:28:12', 1, 1, NULL, NULL, 0, 'user', '2012-05-02 08:28:12', '2012-05-02 08:28:12');
