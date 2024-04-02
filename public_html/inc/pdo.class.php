<?
class myPDO extends PDO
{
	private $sql;
	private $values;
	public $debug = false;

	public function __construct()
	{
		global $config;
		try
		{
			$dns = "mysql:host={$config["db_host"]};dbname={$config["db_name"]}";
			$username = $config["db_user"];
			$password = $config["db_pass"];
			/* $options = array(
							    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
							); */
			parent::__construct($dns, $config["db_user"], $config["db_pass"]);//,$options);

			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
		}
		catch (PDOException $e)
		{
			if ($this->debug)
			{
				die($e->getMessage());
			}
			else
			{
				die("Errore di connessione al server!");

			}
		}
	}

    /**
     * [bindAndExec description]
     * @param  [string] $sql  sqlquery parametrizzata
     * @param  [array] $bind array chiave valore corrispondente ai parametri della query
     * @return [pdo object] risultato della query
     */
		 public function go($sql,$bind = array()) {
			return $this->bindAndExec($sql,$bind);
		 }
    public function bindAndExec($sql,$bind = array())
    {
    	$this->sql = $sql;
    	$this->values = $bind;
    	try
    	{
    		$bind = $this->checkBind($bind);
    		$sth = $this->prepare($sql);
    		$sth->execute($bind);
    		return $sth;
    	}
    	catch (PDOException $e)
    	{
    		if ($this->debug)
    		{
    			echo "Codice: " . $e->getCode() . " --- " . $e->getMessage();
    			$err  = "QUERYSTRING: ".$this->getSQL().PHP_EOL;
    			$err .= "ERROR: ".PHP_EOL.$e->getMessage();
    			echo nl2br($err);
    		}
    		return $sth;
    	}
    }

	/**
	 * [getSQL description]
	 * @return [string] [description]
	 */
	public function getSQL($sql = "", $values = array())
	{
		$sql = $sql != "" ? $sql : $this->sql;
		$values = !empty($values) ? $this->checkBind($values) : $this->checkBind($this->values);
		if (sizeof($values) > 0) {
			$underscored_key = preg_grep("/([a-zA-Z][\w]*)+_/", array_keys($values));
			foreach ($underscored_key as $key)
			{
				$sql = str_replace($key, $this->quote($values[$key]), $sql);
			}
			foreach ($values as $key => $value) {
				$sql = str_replace($key, $this->quote($value), $sql);
			}
		}
		return $sql;
	}

	private function checkBind($bind)
	{
		foreach ($bind as $key => $value) {
			if (substr($key, 0,1) != ":")
			{
				unset($bind[$key]);
				$bind[":".$key] = $value;
			}
		}
		return $bind;
	}
}
?>
