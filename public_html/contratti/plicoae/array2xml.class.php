<?
  /**
   * Convert Array to xml
   */
  class array2xml {

    public $array;
    private $xml;
    private $iterated;

    function __construct($array = array()) {
      $this->xml = new XMLWriter();
      $this->xml->openMemory();
      $this->xml->setIndent(8);
      $this->xml->setIndentString("    ");
      $this->iterated = FALSE;
      if(!empty($array) && is_array($array)) {
        $this->array = $array;
        $this->convertData();
        $this->iterated = TRUE;
      }
    }

    public function getXML() {
      if(!$this->iterated) {
        $this->convertData();
      }
      return $this->xml->outputMemory(TRUE);
    }

    private function convertData() {
      if(empty($this->array)) {
				throw new Exception("No data found!", 1);
			} else {
        $this->iterate($this->array);
      }
    }

    private function iterate($elements) {
      foreach ($elements as $key => $element) {
        if(!empty($element)) {
          if(is_numeric($key) && is_array($element)) {
            $this->xml->startElement($key);
            $this->iterate($element);
            $this->xml->endElement();
          } elseif (is_numeric($key) && !is_array($element) && is_string($element)) {
            continue;
          } elseif($key === "$") {
            $this->xml->text($element);
          } elseif (strpos($key,"@") === 0) {
            $this->xml->writeAttribute(ltrim($key,"@"), $element);
          } else {
            $this->xml->startElement($key);
            if(is_array($element)) {
              $this->iterate($element);
            } else {
              $this->xml->text($element);
            }
            $this->xml->endElement();
          }
        }
      }
    }
  }

?>
