<?
	include_once("../../config.php");
	if (isset($_SESSION["codice_utente"]) || isset($_SESSION["registrazione"])) {
	function rrmdir($dir) {
	    if (is_dir($dir)) {
    	    $objects = scandir($dir);
        	foreach ($objects as $object) {
            	if ($object != "." && $object != "..") {
                	if (filetype($dir . "/" . $object) == "dir") {
                    	rrmdir($dir . "/" . $object);
	                } else {
    	                unlink($dir . "/" . $object);
        	        }
            	}
	        }
        reset($objects);
        rmdir($dir);
   		}
	}

/**
 *
 * Check if all the parts exist, and
 * gather all the parts of the file together
 * @param string $dir - the temporary directory holding all the parts of the file
 * @param string $fileName - the original file name
 * @param string $chunkSize - each chunk size (in bytes)
 * @param string $totalSize - original file size (in bytes)
 */
function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize,$config) {

    // count all the parts of this file
    $total_files = 0;
    foreach(scandir($temp_dir) as $file) {
        if (stripos($file, $fileName) !== false) {
            $total_files++;
        }
    }

    // check that all the parts are present
    // the size of the last part is between chunkSize and 2*$chunkSize
    if ($total_files * $chunkSize >=  ($totalSize - $chunkSize + 1)) {

        // create the final destination file
				if (!isset($_SESSION["codice_utente"])) {
					if (!isset($_SESSION["tmp_codice_utente"])) {
						$under_level = "0";
					} else {
						$under_level = $_SESSION["tmp_codice_utente"];
					}
				} else {
					$under_level = $_SESSION["codice_utente"];
				}
        if (($fp = fopen($config["chunk_folder"]."/".$under_level."/".$fileName, 'w')) !== false) {
            for ($i=1; $i<=$total_files; $i++) {
                fwrite($fp, file_get_contents($temp_dir."/".$fileName.'.part'.$i));
            }
            fclose($fp);
        } else {
            return false;
        }

        // rename the temporary directory (to avoid access from other
        // concurrent chunks uploads) and than delete it
        if (rename($temp_dir, $temp_dir.'_UNUSED')) {
            rrmdir($temp_dir.'_UNUSED');
        } else {
            rrmdir($temp_dir);
        }
    }

}

function checkFileName($name) {
	$return = false;
	$ext = explode(".",$name);
	$ext = end($ext);
	if (strpos($name,"..")===false) {
		$permitted = array("mp4","MP4","jpg","jpeg","png","gif","doc","docx","xlsx","xls","pdf","zip","rar","ods","odt","csv","p7m","xml","txt","rtf","JPG","JPEG","PNG","GIF","DOC","DOCX","XLSX","XLS","PDF","ZIP","RAR","ODS","ODT","CSV","P7M","XML","TXT","RTF");
		if (in_array($ext, $permitted) !== false) $return = true;
	}
	return $return;
}
////////////////////////////////////////////////////////////////////
// THE SCRIPT
////////////////////////////////////////////////////////////////////

//check if request is GET and the requested chunk exists or not. this makes testChunks work
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (!isset($_SESSION["codice_utente"])) {
		if (!isset($_SESSION["tmp_codice_utente"])) {
			$under_level = "0";
		} else {
			$under_level = $_SESSION["tmp_codice_utente"];
		}
	} else {
		$under_level = $_SESSION["codice_utente"];
	}
	if (isset($_GET['resumableFilename']) && checkFileName($_GET['resumableFilename'])) {
    $temp_dir = $config["chunk_folder"]."/".$under_level.'/'.$_GET['resumableIdentifier'];
    $chunk_file = $temp_dir.'/'.$_GET['resumableFilename'].'.part'.$_GET['resumableChunkNumber'];
    if (file_exists($chunk_file)) {
         header("HTTP/1.0 200 Ok");
    } else {
     header("HTTP/1.0 404 Not Found");
    }
	} else {
	  header("HTTP/1.0 500 Forbidden");
		die();
	}
}



// loop through files and move the chunks to a temporarily created directory
if (!empty($_FILES)) foreach ($_FILES as $file) {
    // init the destination file (format <filename.ext>.part<#chunk>
    // the file is stored in a temporary directory
		if (!isset($_SESSION["codice_utente"])) {
			if (!isset($_SESSION["tmp_codice_utente"])) {
				$under_level = "0";
			} else {
				$under_level = $_SESSION["tmp_codice_utente"];
			}
		} else {
			$under_level = $_SESSION["codice_utente"];
		}
		if (checkFileName($_POST['resumableFilename'])) {
	    $temp_dir = $config["chunk_folder"]."/".$under_level . '/' . $_POST['resumableIdentifier'];
	    $dest_file = $temp_dir.'/'.$_POST['resumableFilename'].'.part'.$_POST['resumableChunkNumber'];
	    // create the temporary directory
	    if (!is_dir($temp_dir)) {
	        mkdir($temp_dir,0777,true);
	    }

	    // move the temporary file
	    if (move_uploaded_file($file['tmp_name'], $dest_file)) {
			// check if all the parts present, and create the final destination file
	        createFileFromChunks($temp_dir, $_POST['resumableFilename'],
	        $_POST['resumableChunkSize'], $_POST['resumableTotalSize'],$config);
	    }
		} else {
		  header("HTTP/1.0 500 Forbidden");
			die();
		}
}

}
?>
