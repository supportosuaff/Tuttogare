<?php
	@session_start();
	if (!isset($_SESSION["language"])) $_SESSION["language"] = "IT";
	$available_lg = ["IT","EN"];
	if (isset($_GET["language"]) && in_array($_GET["language"], $available_lg, TRUE) !== false) {
		$_SESSION["language"] = $_GET["language"];
	}
	if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] === "0") {
		ini_set("display_errors", "1");
		error_reporting(E_ALL);
	}


	global $root;
	$root = __DIR__."/public_html";
	ini_set('magic_quotes_runtime', 0);

	$config = array();

	if (file_exists(__DIR__ . "/config.json")) {
		$config = json_decode(file_get_contents(__DIR__ . "/config.json"),true);
	} else {
		die('<h1 style="text-align: center; margin-top: 100px; color: red;">IMPOSSIBILE ACCEDERE ALLA CONFIGURAZIONE DEL SITO.<br><small>IMPOSSIBILE AVVIARE L&#39;APPLICATIVO!!</small></h1>');
	}

	$config["bash_folder"] = __DIR__."/bash";
	$config["cafolder"] = __DIR__."/cainfo";
	$config["doc_folder"] = __DIR__."/buste";
	$config["arch_folder"] = __DIR__."/archivio";
	$config["pub_doc_folder"] = __DIR__."/documenti";
	$config["user_log"] = __DIR__."/userLog";
	$config["create_domain_folder"] = __DIR__."/domini";
	$config["path_vocabolario"] = __DIR__."/vocabolario";
	$config["chunk_folder"] = sys_get_temp_dir();
	
	if (!is_dir($config["cafolder"])) mkdir($config["cafolder"]);
	if (!is_dir($config["doc_folder"])) mkdir($config["doc_folder"]);
	if (!is_dir($config["arch_folder"])) mkdir($config["arch_folder"]);
	if (!is_dir($config["pub_doc_folder"])) mkdir($config["pub_doc_folder"]);
	if (!is_dir($config["user_log"])) mkdir($config["user_log"]);

	$manutenzione = true;
	$bypass = false;

	$hide_amica = false;
	$_SESSION["developEnviroment"] = $config["developEnviroment"];
	define('DEVELOP_ENV', $config["developEnviroment"]);

	$config["protocollo"] = "https://";
	if ($_SESSION["developEnviroment"]) $config["protocollo"] = "http://";
	$config["link_sito"] = $config["protocollo"] . $config["link_sito"];

	$disableCaptcha = $config["disableCaptcha"];
	

	if (!isset($_SESSION["config"])) {
		$_SESSION["config"]["nome_sito"] = $config["nome_sito"];
		$_SESSION["config"]["link_sito"] = $config["link_sito"];
	}

	include_once($root."/inc/pdo.class.php");
	include_once($root."/inc/save.class.php");
	include_once($root."/inc/communicator.class.php");
	include_once($root."/inc/password.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/p7m.class.php");
	include_once(__DIR__."/vendor/autoload.php");
	
	purifyInput($_GET);

	$pdo = new myPDO();
	$pdo->debug = FALSE; // (isset($_SESSION["amministratore"]) && $_SESSION["amministratore"]) ? TRUE : FALSE;

	if (isset($_SESSION["codice_utente"]) && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
		// last request was more than 60 minutes ago
		session_unset();     // unset $_SESSION variable for the run-time
		session_destroy();   // destroy session data in storage
		@session_start();
	}
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

	if (!isset($_SESSION['CREATED'])) {
		$_SESSION['CREATED'] = time();
	} else if (time() - $_SESSION['CREATED'] > 1800) {
			// session started more than 30 minutes ago
			$last_id = session_id();
			session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
			if (isset($_SESSION["codice_utente"]) && $_SESSION["codice_utente"] > 0) {
				$check_UPD = false;
				while(!$check_UPD) {
					$bindUPD = array();
					$bindUPD[":sessionID"] = simple_encrypt(session_id(),$config["enc_key"]);
					$bindUPD[":oldID"] = simple_encrypt($last_id,$config["enc_key"]);
					$bindUPD[":codice_utente"] = $_SESSION["codice_utente"];
					$bindUPD[":agent"] = base64_encode($_SERVER ['HTTP_USER_AGENT']);
					$bindUPD[":ip"] = $_SERVER["REMOTE_ADDR"];
					$ris_UPD = $pdo->bindAndExec("UPDATE b_check_sessions SET sessionID = :sessionID, agent = :agent, ip = :ip WHERE sessionID = :oldID AND codice_utente = :codice_utente ",$bindUPD);
					if ($ris_UPD->rowCount() > 0) $check_UPD = true;
				}
			}
			$_SESSION['CREATED'] = time();  // update creation time
	}
	if (!isset($pagina_login)) $_SESSION["id_sessione"] = md5(session_id());


	if (!file_exists($config["user_log"])) mkdir($config["user_log"], 0770, true);
	if (!empty($_SERVER["HTTP_HOST"]) && !empty($_SERVER["REQUEST_URI"])) $actual_link = $config["protocollo"] . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$todayLog = date("Ymd").".log";
	$todayLogPath = "{$config["user_log"]}/{$todayLog}";
	$yesterdayLog = date("Ymd",strtotime("Yesterday")).".log";
	$yesterdayLogPath = "{$config["user_log"]}/{$yesterdayLog}";
	if (file_exists($yesterdayLogPath) && !$_SESSION["developEnviroment"]) {
		$zip = new ZipArchive;
		if ($zip->open($yesterdayLogPath.".zip", ZipArchive::CREATE) === TRUE)
		{
			$unlink_log = false;
			$unlink_tsr = false;
			$unlink_tsq = false;
			if ($zip->addFile($yesterdayLogPath,$yesterdayLog)) {
				$unlink_log = true;
				if ($zip->addFile($yesterdayLogPath.".tsr",$yesterdayLog.".tsr")) {
					$unlink_tsr = true;
					if ($zip->addFile($yesterdayLogPath.".tsq",$yesterdayLog.".tsq")) {
						$unlink_tsq = true;
					}
				}
			}
			if ($zip->close()) {
				if ($unlink_log) unlink($yesterdayLogPath);
				if ($unlink_tsr) unlink($yesterdayLogPath.".tsr");
				if ($unlink_tsq) unlink($yesterdayLogPath.".tsq");
			}
		}
	}

	if (isset($_SESSION["codice_utente"]) &&
		(!isset($ignoreLog) || (isset($ignoreLog) && $ignoreLog != true))
		&& $_SESSION["developEnviroment"]!=true) {
		$tmp = [];
		$tmp[] = date("Y-m-d H:i:s");
		$tmp[] = $_SESSION["codice_utente"];
		$tmp[] = get_client_ip();
		$tmp[] = $actual_link;
		$message = implode(";",$tmp);
		file_put_contents($todayLogPath, $message."\n", FILE_APPEND);
	}
	if (!isset($elaborazioneApi)) {
		check_session_id();
	}

?>
