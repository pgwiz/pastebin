-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql4.serv00.com
-- Generation Time: Oct 15, 2025 at 07:54 AM
-- Server version: 8.0.39
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `m1396_pastebin`
--

-- --------------------------------------------------------

--
-- Table structure for table `pastes`
--

CREATE TABLE `pastes` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `syntax_language` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'plaintext',
  `views` int DEFAULT '0',
  `shares` int DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pastes`
--

INSERT INTO `pastes` (`id`, `user_id`, `title`, `content`, `created_at`, `username`, `syntax_language`, `views`, `shares`, `is_featured`) VALUES
(5, NULL, 'Gf', 'eF3Us0bPJwM', '2025-06-10 13:06:00', 'Pd', 'plaintext', 1, 0, 0),
(6, NULL, 'whwhw', 'yndum8gHwaf', '2025-06-10 13:10:33', 'hhw', 'plaintext', 1, 0, 0),
(7, NULL, 'gyy', '0WdzgBd2kbb', '2025-06-10 13:11:53', 'yyy', 'plaintext', 1, 0, 0),
(8, NULL, 'Tyy', 'taybfdVeE80', '2025-06-10 13:13:25', 'Gg', 'plaintext', 3, 0, 0),
(9, 3, 'px', 'powershell.exe -ExecutionPolicy Bypass -Command \"iex (curl \'https://gist.githubusercontent.com/pgwiz/6fb96e3b8fd159c3408e58aa1a123b73/raw/f085a954e9ad94f058e0430e59a6e74315f39f5e/pcspecchecker.ps1 \').Content\"', '2025-06-19 11:22:06', '', 'plaintext', 4, 0, 0),
(10, NULL, 'USDT', '0x346a2b7f8099a30fd700d20a9d8c8633d1fc968fe4b34fd2b7532e09839119ad', '2025-06-20 13:48:35', 'Aptos', 'plaintext', 2, 0, 0),
(11, NULL, 'Aptos2', '0x346a2b7f8099a30fd700d20a9d8c8633d1fc968fe4b34fd2b7532e09839119ad', '2025-06-20 14:17:21', 'ACC77990', 'plaintext', 1, 0, 0),
(12, 3, 'expo', 'QcDId-d0d0AZ4yS', '2025-06-24 11:47:10', '', 'plaintext', 10, 0, 0),
(13, NULL, 'sds', 'https://1drv.ms/x/c/aa6ed9875f39a016/EWWx6iS_J9ZLu7-GxWQYuQABTiZjjqa_3lZUE6f3VY_rDA?e=3U1DVG', '2025-06-24 16:27:05', 'xt', 'plaintext', 1, 0, 0),
(14, 4, 'get pc log', '<#\r\n.SYNOPSIS\r\n    Gathers system hardware and memory information and saves it to a log file.\r\n\r\n.DESCRIPTION\r\n    This script collects detailed information about the system, including the operating system,\r\n    processor, physical memory, processor cache (L1, L2, L3), and virtual memory settings.\r\n    The collected data is formatted and written to a timestamped log file named \'SystemInfoLog.txt\'\r\n    on the current user\'s desktop.\r\n\r\n.NOTES\r\n    Author: Gemini\r\n    Version: 1.0\r\n    Date: 2024-10-26\r\n#>\r\n\r\n# --- Configuration ---\r\n# Define the path for the log file on the user\'s Desktop\r\n$LogFile = \"$env:USERPROFILE\\Desktop\\SystemInfoLog.txt\"\r\n\r\n# --- Script Body ---\r\n\r\n# Clear the existing log file if it exists, to start fresh\r\nif (Test-Path $LogFile) {\r\n    Clear-Content $LogFile\r\n}\r\n\r\n# Function to write a formatted line to the log file\r\nfunction Write-Log {\r\n    param (\r\n        [string]$Message\r\n    )\r\n    Add-Content -Path $LogFile -Value $Message\r\n}\r\n\r\n# --- Start Logging ---\r\n\r\n# Write Header\r\nWrite-Log \"============================================================\"\r\nWrite-Log \"*** System Information Log - $(Get-Date -Format \'yyyy-MM-dd HH:mm:ss\') ***\"\r\nWrite-Log \"============================================================\"\r\nWrite-Log \"\" # Add a blank line for readability\r\n\r\n# --- 1. System Overview ---\r\nWrite-Log \"------------------------------------------------------------\"\r\nWrite-Log \"## System Overview\"\r\nWrite-Log \"------------------------------------------------------------\"\r\ntry {\r\n    $computerInfo = Get-ComputerInfo\r\n    Write-Log \"Operating System: $($computerInfo.OsName)\"\r\n    Write-Log \"Processor:        $($computerInfo.CsProcessors.Name)\"\r\n    $totalRamGB = [math]::Round($computerInfo.TotalPhysicalMemory / 1GB, 2)\r\n    Write-Log \"Installed RAM:    $($totalRamGB) GB\"\r\n    Write-Log \"System Type:      $($computerInfo.OsArchitecture)\"\r\n}\r\ncatch {\r\n    Write-Log \"Error gathering system overview: $_\"\r\n}\r\nWrite-Log \"\"\r\n\r\n# --- 2. Processor Cache Information ---\r\nWrite-Log \"------------------------------------------------------------\"\r\nWrite-Log \"## Processor Cache Information\"\r\nWrite-Log \"------------------------------------------------------------\"\r\ntry {\r\n    # Get cache information using WMI (Windows Management Instrumentation)\r\n    $processor = Get-WmiObject -Class Win32_Processor\r\n    # L1 and L2 cache sizes are often not directly available or might be summed per core.\r\n    # This approach gets the reported L2 and L3 cache sizes. L1 is typically not exposed here.\r\n    # Note: Sizes are in KB, so we convert to MB for L3 for better readability.\r\n    Write-Log \"L1 Cache Size:    Not directly available via this WMI class.\"\r\n    Write-Log \"L2 Cache Size:    $($processor.L2CacheSize) KB\"\r\n    $l3CacheMB = $processor.L3CacheSize / 1024\r\n    Write-Log \"L3 Cache Size:    $($l3CacheMB) MB\"\r\n}\r\ncatch {\r\n    Write-Log \"Error gathering processor cache info: $_\"\r\n}\r\nWrite-Log \"\"\r\n\r\n# --- 3. Virtual Memory Information ---\r\nWrite-Log \"------------------------------------------------------------\"\r\nWrite-Log \"## Virtual Memory Information\"\r\nWrite-Log \"------------------------------------------------------------\"\r\ntry {\r\n    $osInfo = Get-CimInstance -ClassName Win32_OperatingSystem\r\n    $pageFile = Get-CimInstance -ClassName Win32_PageFileUsage | Select-Object Name, AllocatedBaseSize\r\n\r\n    $totalVirtualMemGB = [math]::Round($osInfo.TotalVirtualMemorySize / 1MB, 2)\r\n    $availableVirtualMemGB = [math]::Round($osInfo.FreeVirtualMemory / 1MB, 2)\r\n\r\n    Write-Log \"Total Virtual Memory:    $($totalVirtualMemGB) GB\"\r\n    Write-Log \"Available Virtual Memory: $($availableVirtualMemGB) GB\"\r\n    Write-Log \"Page File Location:      $($pageFile.Name)\"\r\n    Write-Log \"Page File Size (Initial):  $($pageFile.AllocatedBaseSize) MB\"\r\n}\r\ncatch {\r\n    Write-Log \"Error gathering virtual memory info: $_\"\r\n}\r\nWrite-Log \"\"\r\n\r\n\r\n# --- 4. Current Memory Usage ---\r\nWrite-Log \"------------------------------------------------------------\"\r\nWrite-Log \"## Current Memory Usage\"\r\nWrite-Log \"------------------------------------------------------------\"\r\ntry {\r\n    $memInfo = Get-CimInstance -ClassName Win32_OperatingSystem\r\n    $totalPhysMB = [math]::Round($memInfo.TotalVisibleMemorySize / 1024)\r\n    $availPhysMB = [math]::Round($memInfo.FreePhysicalMemory / 1024)\r\n    $inUseMB = $totalPhysMB - $availPhysMB\r\n\r\n    Write-Log \"Total Physical Memory:   $($totalPhysMB) MB\"\r\n    Write-Log \"Available Physical Memory: $($availPhysMB) MB\"\r\n    Write-Log \"Memory In Use:           $($inUseMB) MB\"\r\n}\r\ncatch {\r\n    Write-Log \"Error gathering current memory usage: $_\"\r\n}\r\nWrite-Log \"\"\r\n\r\n\r\n# --- Write Footer ---\r\nWrite-Log \"============================================================\"\r\nWrite-Log \"                     *** End of Log ***\"\r\nWrite-Log \"============================================================\"\r\n\r\n# --- Final Output ---\r\nWrite-Host \"System information log has been successfully created at:\"\r\nWrite-Host $LogFile -ForegroundColor Green\r\n\r\n', '2025-07-08 07:46:29', '', 'plaintext', 1, 0, 0),
(15, 4, 'Operating System Logs', 'File and Directory Operations Log\r\n\r\nDate: 22/05/2025\r\n\r\nAction: CREATE_DIRECTORY\r\n\r\nDetails: Created a new folder named \"Project_Files\" in C:\\Users\\Admin\\Documents.\r\n\r\nDate: 22/05/2025\r\n\r\nAction: MOVE_FILE\r\n\r\nDetails: Moved draft_report.docx from the Desktop to C:\\Users\\Admin\\Documents\\Project_Files.\r\n\r\nDate: 23/05/2025\r\n\r\nAction: RENAME_FILE\r\n\r\nDetails: Renamed draft_report.docx to final_report_v1.docx.\r\n\r\nDate: 23/05/2025\r\n\r\nAction: DELETE_FILE\r\n\r\nDetails: Deleted an old file located at C:\\Downloads\\temp_installer.exe.\r\n\r\n}', '2025-07-08 08:15:17', '', 'plaintext', 3, 0, 0),
(16, NULL, 'aptos', '0x9ff1a50b4f5b22c7b59bc98f2cdf7d403611eb98fe88d0cc6309178386bc01d8', '2025-07-11 06:40:16', 'pttt', 'plaintext', 4, 0, 0),
(17, NULL, 'Aptos', '0xa08fee847749db3f70c7a8c45931251530d4ab5405a5f40562d11bb417e1e7be', '2025-07-11 07:27:08', 'Brio', 'plaintext', 3, 1, 0),
(18, NULL, 'scrapper', 'https://webscraper.io/tutorials/create-a-sitemap', '2025-07-19 13:25:03', 'webscrapper', 'xml', 1, 1, 0),
(19, NULL, 'excel', 'Of course. Here is the data from the image converted into a format suitable for an Excel sheet.\r\nKAKAMEGA SECURITY WELFARE GROUP\r\n| NO | POST | NAME | GROUP NUMBER | ID NUMBER |\r\n|---|---|---|---|---|\r\n|  | CHAIRMAN | ISAIAB MATINDU | 0713856330 | 114728084 |\r\n|  | ASS. CHAIRMAN | FAUSTINE LIARONELA | 0729132782 | 11416145 |\r\n|  | SECRETARY | NANCY SAKWA | 0726435236 | 23147388 |\r\n|  | ASS. SECRETARY | YONA VIKIRU | 0717992... * | 669137 |\r\n|  | TREASURER | EDA ABDALA | 0713467816 | 21727947 |\r\n|  | ASS. TREASURER | GEDION AKHONYA | 0711517154 | 11834033 |\r\n|  | ASS. ORG. SEC | JULIA ASEMBO | 0713909811 | 22486713 |\r\n|  | CHAPLIN | ALEX MUSEVE | 0723937017 | 2302457 |\r\n|  | MEMBER | JACTON MAKACHIA | 0726336500 | 22046381 |\r\n| 10 | MEMBER | CATHERINE AMEYO | 0791723855 | 22043055 |\r\n| 11 | MEMBER | SILVANUS OKUMUOY | 0718646015 | 20155154 * |\r\n| 12 | MEMBER | JAMES AMUKONGO | 0724272367 | 12911423 |\r\n| 13 | MEMBER | NATHAN SHIKHOLO | 0712628948 | 27190223 |\r\n| 14 | MEMBER | ANN BARAZA | 0705056423 | 2314878 |\r\n| 15 | MEMBER | CAROLYNE KHALIBWA | 0713203444 | 24664312 |\r\n| 16 | MEMBER | ROBAI BUSHURU | 0728676146 | 28012422 |\r\n| 17 | MEMBER | STANLEY SIFUNA | 0740338164 | 37299065 |\r\n| 18 | MEMBER | JOSEPH KIRUI | 0710132034 | 32211161 |\r\n| 19 | MEMBER | BENARD SHILOVYA | 0713203434 | 24728355 |\r\n| 20 | MEMBER | MATROBA ASEMBI | 0792796178 | 11682383 |\r\n| 21 | MEMBER | GRACE ALI | 0790794859 | 23235722 |\r\n| 22 | MEMBER | EUNICE CHIMASIA | 0795071851 | 29874347 |\r\n| 23 | MEMBER | JANE MUKHWANA | 0706738514 | 26782260 |\r\n| 24 | MEMBER | PHILIP MALOBA | 0724479425 | 22137480 |\r\n| 25 | MEMBER | TUKONA KAVIMBA | 0724135921 | 27240590 |\r\n| 26 | MEMBER | ONEMAS OMUKANDA | 0700485920 | 30246512 |\r\n| 27 | MEMBER | SYLVIA LUMIRE | 0700470001 | 29171205 |\r\n| 28 | MEMBER | CRISTABEL ODONGO | 0726595996 | 25581981 |\r\n| 29 | MEMBER | STANLEY KOECH | 0726650711 | 29330558 |\r\nNote: Some numbers were partially illegible in the original document and have been transcribed as accurately as possible.\r\n', '2025-07-26 07:23:38', 'excel', 'plaintext', 2, 0, 0),
(20, NULL, 'tr', 'https://g.co/gemini/share/9451b3939944', '2025-07-26 07:27:16', 'ff', 'plaintext', 0, 0, 0),
(21, 5, 'pika1', 'https://g.co/gemini/share/f8ae60719b0e', '2025-07-26 07:41:31', '', 'plaintext', 1, 0, 0),
(22, NULL, 'ty', '[\r\n  {\r\n    \"category\": \"President\",\r\n    \"name\": \"Wambui Mwangi\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"United Kenya Front\",\r\n    \"symbol\": \"Lion\",\r\n    \"age\": 52,\r\n    \"hometown\": \"Kiambu\",\r\n    \"slogan\": \"Hands Together, Kenya Forward!\",\r\n    \"photo_prompt\": \"A realistic, confident Kenyan woman in her 50s, standing alone in front of Mount Kenya at sunrise; wearing a tailored olive-green blazer with a lion lapel pin, holding a constitution book, friendly but serious expression, neat afro-chic bun, campaign poster style with modern patterns.\",\r\n    \"bio\": \"Former Minister of Education and grassroots activist. Advocate for digital schools and maternal healthcare reform. Mother of three. Speaks 5 local dialects fluently.\",\r\n    \"manifesto_highlight\": \"United Kenya Front — Lion Symbol: Strength in Unity. Priorities: Digital Inclusion, Green Industrialization, Gender Equity, Devolution Empowerment, Youth Employment.\",\r\n    \"design_theme\": \"Modern Afro-futurist, elegant color gradients, clean gold and earth tones, lion imagery, clear English and Swahili text.\"\r\n  },\r\n  {\r\n    \"category\": \"President\",\r\n    \"name\": \"Baraza Ochieng\'\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"Progressive Peoples Party\",\r\n    \"symbol\": \"Eagle\",\r\n    \"age\": 58,\r\n    \"hometown\": \"Kisumu\",\r\n    \"slogan\": \"Unity in Our Grip, Progress in Our Step!\",\r\n    \"photo_prompt\": \"A realistic older Kenyan man with salt-and-pepper goatee, alone on Lake Victoria shore at dusk, wearing a navy suit and eagle-shaped armband, holding a blueprint, dignified, focused face, campaign style poster, minimalist gold accent border.\",\r\n    \"bio\": \"Infrastructure czar and former Governor. Champion of lake basin economic zones and green energy corridors. Married, father of twins. Founded 12 youth tech hubs.\",\r\n    \"manifesto_highlight\": \"Progressive Peoples Party — Eagle Symbol: Vision for Tomorrow. Priorities: Digital Inclusion, Green Industrialization, Gender Equity, Devolution Empowerment, Youth Employment.\",\r\n    \"design_theme\": \"Modern Afro-futurist, sunset gradients, navy-gold tones, distinct eagle motif, bilingual clear text.\"\r\n  },\r\n  {\r\n    \"category\": \"President\",\r\n    \"name\": \"Zawadi Mwaura\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Alliance for Progress\",\r\n    \"symbol\": \"Tree\",\r\n    \"age\": 47,\r\n    \"hometown\": \"Meru\",\r\n    \"slogan\": \"Her Hands, Your Future!\",\r\n    \"photo_prompt\": \"Realistic woman mid-40s, alone, smiling with energy, in a burgundy dashiki suit, standing among green tea fields at sunset, waving a tree-shaped party banner, modern campaign poster look.\",\r\n    \"bio\": \"Agri-tech entrepreneur and climate resilience advocate. Built Kenya’s largest women-led export cooperative. TEDx speaker, climate expert.\",\r\n    \"manifesto_highlight\": \"Alliance for Progress — Tree Symbol: Roots of Growth. Priorities: Digital Inclusion, Green Industrialization, Gender Equity, Devolution Empowerment, Youth Employment.\",\r\n    \"design_theme\": \"Modern Afro-futurist, lush green/earth gradients, tree icon, crisp readable layout.\"\r\n  },\r\n  {\r\n    \"category\": \"Governor\",\r\n    \"name\": \"Makena Kariuki\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"United Kenya Front\",\r\n    \"symbol\": \"Sun\",\r\n    \"age\": 44,\r\n    \"hometown\": \"Nyeri\",\r\n    \"slogan\": \"Govern with Grace, Lead with Grit!\",\r\n    \"photo_prompt\": \"Realistic, professional Kenyan woman in grey suit, alone beside a coffee cooperative sign, holding a sun-shaped tablet displaying county dashboard, mountain mist in background, calm smile, clean campaign poster border.\",\r\n    \"bio\": \"Tech-focused administrator and ex-county CIO. Led service reforms. Married, mother of one.\",\r\n    \"manifesto_highlight\": \"United Kenya Front — Sun Symbol: Bright Future, Fair Leadership. Priorities: Digital Inclusion, Service Reform, Devolution.\",\r\n    \"design_theme\": \"Afro-futurist with sunrise accents, gold/grey/green palette, sun icon, bilingual text.\"\r\n  },\r\n  {\r\n    \"category\": \"Governor\",\r\n    \"name\": \"Jabali Mwenda\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"Progressive Peoples Party\",\r\n    \"symbol\": \"Anchor\",\r\n    \"age\": 50,\r\n    \"hometown\": \"Mombasa\",\r\n    \"slogan\": \"Coastal Power, Continental Vision!\",\r\n    \"photo_prompt\": \"A realistic coastal Kenyan man, alone, on a Dhow dock at sunset, crisp white linen suit, anchor lapel pin, arms wide in welcome, warm confident face, campaign design with ocean colors.\",\r\n    \"bio\": \"Port modernization expert and economist. Led tourism growth. Widower, four children.\",\r\n    \"manifesto_highlight\": \"Progressive Peoples Party — Anchor Symbol: Stability, Progress, Inclusion.\",\r\n    \"design_theme\": \"Modern marine-inspired gradient, blue/white/gold, anchor icon, clean text layout.\"\r\n  },\r\n  {\r\n    \"category\": \"Governor\",\r\n    \"name\": \"Asha Farah\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Alliance for Progress\",\r\n    \"symbol\": \"Water Droplet\",\r\n    \"age\": 39,\r\n    \"hometown\": \"Garissa\",\r\n    \"slogan\": \"Desert Blooms Under Her Care!\",\r\n    \"photo_prompt\": \"Realistic Somali-Kenyan woman, modern teal suit, standing alone in front of solar water plant, gold water droplet pin, gentle smile, evening desert light, focused campaign poster.\",\r\n    \"bio\": \"Energy pioneer and nutrition advocate. Built solar clinics. Dedicates life to service.\",\r\n    \"manifesto_highlight\": \"Alliance for Progress — Water Droplet Symbol: Clean Water, New Growth.\",\r\n    \"design_theme\": \"Blue-gold desert gradient, soft border, water drop symbol, bilingual headlines.\"\r\n  },\r\n  {\r\n    \"category\": \"Senator\",\r\n    \"name\": \"Thandiwe Odhiambo\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"United Kenya Front\",\r\n    \"symbol\": \"Shield\",\r\n    \"age\": 46,\r\n    \"hometown\": \"Kisii\",\r\n    \"slogan\": \"Voice of the Valley, Heart of the Nation!\",\r\n    \"photo_prompt\": \"Realistic, elegant woman alone in wine-red blazer, standing above Nairobi skyline from Senate balcony, gold shield brooch, dignified, calm campaign layout.\",\r\n    \"bio\": \"Constitutional lawyer and devolution expert. Authored landmark bills. Single, mentor.\",\r\n    \"manifesto_highlight\": \"United Kenya Front — Shield Symbol: Protection for Equity.\",\r\n    \"design_theme\": \"Red-gold gradient, shield accent, structured modern text, inclusive look.\"\r\n  },\r\n  {\r\n    \"category\": \"Senator\",\r\n    \"name\": \"Malik Omondi\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"Progressive Peoples Party\",\r\n    \"symbol\": \"Handshake\",\r\n    \"age\": 53,\r\n    \"hometown\": \"Busia\",\r\n    \"slogan\": \"Borderless Progress, Rooted in Justice!\",\r\n    \"photo_prompt\": \"Realistic older Kenyan man alone at border marker, charcoal suit, handshake motif lapel, holding agreement document, clear, thoughtful campaign style.\",\r\n    \"bio\": \"Trade and integration architect, lecturer, columnist.\",\r\n    \"manifesto_highlight\": \"Progressive Peoples Party — Handshake Symbol: Collaboration and Growth.\",\r\n    \"design_theme\": \"Charcoal-gold fade, handshake icon, readable fonts.\"\r\n  },\r\n  {\r\n    \"category\": \"Senator\",\r\n    \"name\": \"Nyota Wanjiru\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Alliance for Progress\",\r\n    \"symbol\": \"Star\",\r\n    \"age\": 41,\r\n    \"hometown\": \"Laikipia\",\r\n    \"slogan\": \"Stars Align When Women Lead!\",\r\n    \"photo_prompt\": \"Realistic mid-aged Kenyan woman in navy suit, standing solo under a starry sky with telescope, gold star lapel, arms crossed in confident pose, poster layout star accents.\",\r\n    \"bio\": \"Astrophysicist and STEM advocate. Founded girls’ space-tech NGO. Forbes Africa 30 Under 40.\",\r\n    \"manifesto_highlight\": \"Alliance for Progress — Star Symbol: Innovation and Possibility.\",\r\n    \"design_theme\": \"Night-sky gradients, star icon, crisp multilingual text.\"\r\n  },\r\n  {\r\n    \"category\": \"Member of Parliament\",\r\n    \"name\": \"Chomba Mutua\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"United Kenya Front\",\r\n    \"symbol\": \"Gear\",\r\n    \"age\": 37,\r\n    \"hometown\": \"Machakos\",\r\n    \"slogan\": \"Youth Engine, National Drive!\",\r\n    \"photo_prompt\": \"Realistic young Kenyan man, slim-fit suit, standing alone before tech hub mural holding a gear-shaped drone, energetic, tech-inspired campaign imagery.\",\r\n    \"bio\": \"Startup founder, created 5,000 apprenticeships, podcast host.\",\r\n    \"manifesto_highlight\": \"United Kenya Front — Gear Symbol: Innovation for All.\",\r\n    \"design_theme\": \"Urban-tech color fade, gear icon, direct text alignment.\"\r\n  },\r\n  {\r\n    \"category\": \"Member of Parliament\",\r\n    \"name\": \"Faida Hassan\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Progressive Peoples Party\",\r\n    \"symbol\": \"Camel\",\r\n    \"age\": 43,\r\n    \"hometown\": \"Isiolo\",\r\n    \"slogan\": \"Pastoral Prosperity, Modern Mandate!\",\r\n    \"photo_prompt\": \"Realistic Somali-Kenyan woman in modern dress, holding a camel-shaped tablet, alone beside dairy cooperative at dawn, campaign poster frame.\",\r\n    \"bio\": \"Economist, advocates agribusiness and infrastructure, two children.\",\r\n    \"manifesto_highlight\": \"Progressive Peoples Party — Camel Symbol: Endurance and Growth.\",\r\n    \"design_theme\": \"Earth-tone gradient, camel insignia, accessible bilingual font.\"\r\n  },\r\n  {\r\n    \"category\": \"Member of Parliament\",\r\n    \"name\": \"Kiprono Bett\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"Alliance for Progress\",\r\n    \"symbol\": \"Leaf\",\r\n    \"age\": 49,\r\n    \"hometown\": \"Bomet\",\r\n    \"slogan\": \"Tea, Tech, and Tomorrow!\",\r\n    \"photo_prompt\": \"Realistic, grounded Kenyan man in green jacket, holding smartphone and leaf symbol, alone in misty tea estate sunrise, campaign layout.\",\r\n    \"bio\": \"Climate-smart farming advocate, family man.\",\r\n    \"manifesto_highlight\": \"Alliance for Progress — Leaf Symbol: Green growth, smart future.\",\r\n    \"design_theme\": \"Green mist colorway, leaf iconography, sharp fonts.\"\r\n  },\r\n  {\r\n    \"category\": \"Women Rep\",\r\n    \"name\": \"Adhiambo Okech\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"United Kenya Front\",\r\n    \"symbol\": \"Book\",\r\n    \"age\": 40,\r\n    \"hometown\": \"Homa Bay\",\r\n    \"slogan\": \"Her Voice, Her Rights, Her County!\",\r\n    \"photo_prompt\": \"Realistic, strong woman in purple dress, alone at a vibrant market, holding a book symbol, arms raised in empowering pose, campaign poster light flare.\",\r\n    \"bio\": \"Microfinance legal activist, single mother.\",\r\n    \"manifesto_highlight\": \"United Kenya Front — Book Symbol: Education and Fairness.\",\r\n    \"design_theme\": \"Purple-gold fade, book emblem, clear readable layout.\"\r\n  },\r\n  {\r\n    \"category\": \"Women Rep\",\r\n    \"name\": \"Saida Juma\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Progressive Peoples Party\",\r\n    \"symbol\": \"Coral\",\r\n    \"age\": 36,\r\n    \"hometown\": \"Lamu\",\r\n    \"slogan\": \"Tides of Change, Led by Her!\",\r\n    \"photo_prompt\": \"Realistic young coastal woman in pale indigo abaya, standing alone on Lamu street, holding a coral-shaped tablet, fresh campaign vibe.\",\r\n    \"bio\": \"Teen girls coding advocate, marine conservation.\",\r\n    \"manifesto_highlight\": \"Progressive Peoples Party — Coral Symbol: Healing, Renewal.\",\r\n    \"design_theme\": \"Indigo-sand blend, coral shape, simple bilingual headings.\"\r\n  },\r\n  {\r\n    \"category\": \"Women Rep\",\r\n    \"name\": \"Waceke Kimani\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Alliance for Progress\",\r\n    \"symbol\": \"Seedling\",\r\n    \"age\": 45,\r\n    \"hometown\": \"Murang’a\",\r\n    \"slogan\": \"Mother Earth, Mother Governance!\",\r\n    \"photo_prompt\": \"Realistic, nurturing woman kneeling alone in a garden at golden hour, holding a seedling symbol, serene campaign photo style.\",\r\n    \"bio\": \"Environmental health and maternal advocate.\",\r\n    \"manifesto_highlight\": \"Alliance for Progress — Seedling Symbol: Nurturing for New Generations.\",\r\n    \"design_theme\": \"Soft earth gradient, seedling design, crisp dual-language text.\"\r\n  },\r\n  {\r\n    \"category\": \"Member of County Assembly\",\r\n    \"name\": \"Riziki Mwita\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"United Kenya Front\",\r\n    \"symbol\": \"Laptop\",\r\n    \"age\": 33,\r\n    \"hometown\": \"Vihiga\",\r\n    \"slogan\": \"Small Office, Big Impact!\",\r\n    \"photo_prompt\": \"Realistic young Kenyan man, casual blazer, standing alone at a local market, holding a laptop symbol, bright, businesslike campaign photo.\",\r\n    \"bio\": \"Market digitization mobilizer, DJ, assemblyman.\",\r\n    \"manifesto_highlight\": \"United Kenya Front — Laptop Symbol: Digital Solutions, Economic Growth.\",\r\n    \"design_theme\": \"Light blue-gold color mix, laptop icon, young fresh poster design.\"\r\n  },\r\n  {\r\n    \"category\": \"Member of County Assembly\",\r\n    \"name\": \"Zamzam Ali\",\r\n    \"gender\": \"Female\",\r\n    \"party\": \"Progressive Peoples Party\",\r\n    \"symbol\": \"Key\",\r\n    \"age\": 31,\r\n    \"hometown\": \"Mandera\",\r\n    \"slogan\": \"From the Margins to the Mainstream!\",\r\n    \"photo_prompt\": \"Realistic young Somali woman, modern suit and bright orange hijab, standing alone by a new school, holding a large symbolic golden key, campaign layout.\",\r\n    \"bio\": \"School infrastructure advocate, unmarried, youth influencer.\",\r\n    \"manifesto_highlight\": \"Progressive Peoples Party — Key Symbol: Unlocking Opportunity.\",\r\n    \"design_theme\": \"Magenta-gold colorway, key emblem, bold clear text.\"\r\n  },\r\n  {\r\n    \"category\": \"Member of County Assembly\",\r\n    \"name\": \"Tuitoek Koech\",\r\n    \"gender\": \"Male\",\r\n    \"party\": \"Alliance for Progress\",\r\n    \"symbol\": \"Map\",\r\n    \"age\": 38,\r\n    \"hometown\": \"Baringo\",\r\n    \"slogan\": \"Water, Wisdom, Ward Development!\",\r\n    \"photo_prompt\": \"Realistic man in safari shirt, standing alone beside solar borehole with map symbol, focused, optimistic campaign setting.\",\r\n    \"bio\": \"Water resource engineer, resilience expert.\",\r\n    \"manifesto_highlight\": \"Alliance for Progress — Map Symbol: Solutions for Every Ward.\",\r\n    \"design_theme\": \"Ochre-blue color gradient, map motif, clean modern type.\"\r\n  }\r\n]\r\n', '2025-09-22 16:26:31', 'json', 'plaintext', 2, 0, 0),
(23, NULL, 'akajka', 'You are an AI poster editor.  \r\n\r\nYour task is to update existing Kenya election campaign posters by replacing or adding text while preserving the original design, layout, and quality.\r\n\r\n\r\n\r\nInstructions:\r\n\r\n1. Input: You will receive an existing poster image + a JSON with updated text details.  \r\n\r\n2. Preserve: Keep all backgrounds, candidate images, colors, and design elements intact. Do not distort or blur the poster.  \r\n\r\n3. Replace: Change only the text elements specified in the JSON — such as \"name\", \"slogan\", \"party\", \"manifesto_highlight\", or \"category\".  \r\n\r\n4. Styling: Match the typography, color, and placement of the original text so the edit looks seamless.  \r\n\r\n5. Bilingual Rule: If the JSON specifies bilingual typography, apply it consistently in the text update.  \r\n\r\n6. Output: A polished campaign poster where only the text is changed, but the visual style remains authentic and professional.', '2025-09-22 16:27:51', 'pppp', 'plaintext', 1, 0, 0),
(24, NULL, 'akhak', 'You are a professional political campaign poster designer.  \r\n\r\nUse the following JSON data to create a **Kenya election–themed poster**:\r\n\r\n\r\n\r\n{JSON_INPUT}\r\n\r\n\r\n\r\nGenerate a poster where:  \r\n\r\n- The candidate’s image (from \"photo_prompt\") covers at least 3/4 of the page.  \r\n\r\n- Apply the \"design_theme\" for overall visual direction.  \r\n\r\n- Prominently display candidate details: name, category, party, slogan, and manifesto highlight.  \r\n\r\n- Integrate the party \"symbol\" subtly into the layout.  \r\n\r\n- Ensure the final design is polished, professional, and culturally resonant for Kenyan elections.', '2025-09-22 16:32:49', 'sjhdkjd', 'plaintext', 2, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `paste_permissions`
--

CREATE TABLE `paste_permissions` (
  `id` int NOT NULL,
  `paste_id` int NOT NULL,
  `user_id` int NOT NULL,
  `permission_type` varchar(20) NOT NULL DEFAULT 'edit',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avatar_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `avatar_url`, `email`, `is_superadmin`) VALUES
(3, 'pgwiz', '$2y$10$cbf1wAb5Qwbs/Xt3mXdfjORTDC7c39qkNCBSdvmpv6mLsuOqULeki', '2025-06-19 11:21:24', NULL, 'pgwiz@cdpzi.0i6', 0),
(4, 'pox', '$2y$10$zOf7SxcnW9u2e/GK997ObOQYaG.qf.Y4YkYWFNFxWxwvM3oKiOepS', '2025-07-08 07:45:28', NULL, 'px@px.px', 0),
(5, 'pikachu', '$2y$10$Mn57HYOoMXGo2CVrfiWxR.pemG0RbWsgbad3jGpclgdsWmkP74X9.', '2025-07-26 07:40:56', NULL, 'pikachu970@gmail.com', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pastes`
--
ALTER TABLE `pastes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `paste_permissions`
--
ALTER TABLE `paste_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permission` (`paste_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pastes`
--
ALTER TABLE `pastes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `paste_permissions`
--
ALTER TABLE `paste_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pastes`
--
ALTER TABLE `pastes`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pastes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `paste_permissions`
--
ALTER TABLE `paste_permissions`
  ADD CONSTRAINT `paste_permissions_ibfk_1` FOREIGN KEY (`paste_id`) REFERENCES `pastes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paste_permissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
