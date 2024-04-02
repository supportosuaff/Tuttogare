<?

class salva {

	public  $debug;
	public 	$errore;
	public 	$nome_tabella;
	public  $codop;
	private $codice;
	public 	$operazione;
	public 	$oggetto;
	public 	$return;
	public 	$ignorePurify;
	private $db;
	/**
	 * Stabilisco la connesione al database
	 * quando istanzio la classe
	 */
	function __construct()
	{
		global $pdo;
		$this->ignorePurify = false;
		if (isset($pdo)) {
			$this->db = $pdo;
		} else {
			$this->db = new myPDO();
		}
	}

	/**
	 * Chiudo la connessione al database
	 * quando la classe finisce tutte le operazioni
	 */
	function __destruct()
	{
		$this->db = null;
	}

	/**
	 * Metodo init()
	 * @return Se presente un errore ritorna un messaggio
	 */
	private function init()
	{
		$error = false;
		$message = '';

		if (is_null($this->debug))
		{
			$this->debug = false;
		}
		if (is_null($this->codop))
		{
			$error = true;
			$message .= "Errore di Inizializzazione. Codice Operatore Assente" . PHP_EOL;

		}
		if (is_null($this->nome_tabella) || $this->nome_tabella == "")
		{
			$error = true;
			$message .= "Errore di Inizializzazione. Tabella non definita" . PHP_EOL;
		}
		if (!in_array($this->operazione, array("UPDATE","INSERT")))
		{
			$error = true;
			$message .= "Errore di Inizializzazione. Operazione non riconosciuta" . PHP_EOL;
		}
		if ($this->operazione == "UPDATE" && ( !isset($this->oggetto["codice"]) || $this->oggetto["codice"] == 0 || $this->oggetto["codice"] == "" ) )
		{
			$error = true;
			$message .= "Errore di Inizializzazione. Codice non valido" . PHP_EOL; }

		if (!is_array($this->oggetto))
		{
			$error = true;
			$message .= "Errore di Inizializzazione. Oggetto non valido" . PHP_EOL; }

		if ($this->operazione == "UPDATE")
		{
			$this->codice = $this->oggetto["codice"];
		}
		if (!is_null($this->codop)) $this->oggetto["utente_modifica"] = $this->codop;
		if ($error && $message != "")
		{
			if ($this->debug)
			{
				exit($message);
			}
			else
			{
				exit();
			}
		}
	}
  private function purify($text) {
		if (!$this->ignorePurify) {
			$config = HTMLPurifier_Config::createDefault();
			// $config->set('Core', 'Encoding', 'ISO-8859-1'); // replace with your encoding
			// $config->set('HTML', 'Doctype', 'HTML 4.01 Transitional'); // replace with your doctype
			$purifier = new HTMLPurifier($config);
			return $purifier->purify($text);
		} else {
			return $text;
		}
	}
	private function setPost()
	{
		$data = array();
		$sql_field = "SHOW FIELDS FROM `$this->nome_tabella` WHERE `Field` = :field_name";
		$sth_field = $this->db->prepare($sql_field);

		$intTypes = array("integer", "int", "smallint", "tinyint", "mediumint", "bigint");
		$decTypes = array("decimal", "numeric", "float", "double");

		foreach ($this->oggetto as $field => $value)
		{
			try
			{
				$sth_field->execute(array(":field_name" => $field));
				$rec_field = $sth_field->fetch(PDO::FETCH_ASSOC);
				if (strpos($rec_field["Type"],"(")!==false) {
					$rec_field["Type"] = explode("(", $rec_field["Type"]);
					$rec_field["Type"] = $rec_field["Type"][0];
				}
				if (is_array($rec_field))
				{
					switch ($rec_field["Type"])
					{
						case 'date':
							$data[$field] = date2mysql($value);
							break;
						case 'datetime':
							$data[$field] = datetime2mysql($value);
							break;
						default:
							if (stripos($rec_field["Type"], "BLOB") === false) {
								if (stripos($rec_field["Type"],"TEXT")===FALSE) {
									if (in_array($rec_field["Type"],$intTypes)!==FALSE) {
										if (!is_numeric($value) || strpos($value,".")!==false) $value = "";
									} else if (in_array($rec_field["Type"],$decTypes)!==FALSE) {
										if (!is_numeric($value)) $value = "";
									}
									$value = $this->purify($value);
                  // $value = htmlentities($value);
									$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
									$data[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                } else {
								  $data[$field] = $this->purify($value);
                }
							} else {
								$data[$field] = $value;
							}
							break;
					}
				}
			}
			catch (PDOException $error)
			{
				if ($this->debug)
				{
					echo str_replace(":field_name", $field, $sql_field);
					exit("PDOEXCEPTION: UNABLE TO FIND FIELDS IN TABLE $this->nome_tabella" . PHP_EOL . $error->getMessage());
				}
				else
				{
					exit();
				}
			}
		}
		return $data;
	}

	private function prepareQuery($data)
	{
		$prepared_query = "";
		if (count($data)>0)
		{
			if ($this->operazione == "INSERT")
			{
				$prepared_query = "INSERT INTO " . $this->nome_tabella . " (`".implode('`, `',array_keys($data)).'`) VALUES (:'.implode(', :',array_keys($data)).')';
			}
			elseif($this->operazione == "UPDATE")
			{
				$update_stmt = "";
				foreach ($data as $key => $value) {
					$update_stmt .= '`'.$key.'`' . ' = :' . $key . ', ';
				}
				$prepared_query = "UPDATE " . $this->nome_tabella . " SET " . substr($update_stmt,0,-2) . " WHERE `codice` = :codice";
			}

			if ($prepared_query != "")
			{
				if ($this->debug) echo "PREPAREDQUERY: " . $prepared_query . PHP_EOL;
				return $prepared_query;
			}
			else
			{
				if ($this->debug) echo "ERRORE DI CREAZIONE SQL QUERY" . PHP_EOL;
				$this->errore = "SQL ERROR";
				$this->return =  false;
			}

		}
		else
		{
			if ($this->debug) echo "OGGETTO VUOTO" . PHP_EOL;
			$this->errore = "DATA ERROR";
			$this->return =  false;
		}
	}

	private function bindData($data)
	{
		$bind = array();
		foreach ($data as $field => $value)
		{
			$bind[":".$field] = ($value !== "" ? $value : null);

		}
		return $bind;
	}

	public function save()
	{

		if (!$this->init())
		{
			$data = $this->setPost();
			$prepared_query = $this->prepareQuery($data);
			$bind = $this->bindData($data);

			try
			{
				$stmt = $this->db->prepare($prepared_query);
				$stmt->execute($bind);

				if ($this->operazione == "INSERT") {
					$this->codice = $this->db->lastInsertId();
					if ($this->nome_tabella == "b_allegati") {
						if (class_exists("syncERP")) {
							$sync = new syncERP();
							$sync->sendAllegato($this->codice);
						}
					}
				}

				scrivilog($this->nome_tabella,$this->operazione,$this->db->getSQL($prepared_query,$bind),($this->codop));
				$this->return = $this->codice;

			}
			catch (PDOException $error)
			{
				if ($this->debug) echo "Codice: " . $error->getCode() . " --- " . $error->getMessage();
				$this->errore = "SQL STATEMENT ERROR";
				$this->return =  false;
			}

			if ($this->debug) echo $this->errore;
			return $this->return;
			$db = null;
		}

	}
}

?>
