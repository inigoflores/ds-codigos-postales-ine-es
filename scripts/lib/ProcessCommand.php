<?php

require_once('Config.php');

use ConsoleKit\Widgets\ProgressBar;

/**
 * Procesa los archivos fuente y graba a disco los datos en CSV
 *
 */
class ProcessCommand extends ConsoleKit\Command
{


    /*
     * Overriding para que invoque all por defecto.
     */
    public function execute(array $args, array $options = array())
    {
        //si se invoca sin sucomando, ejecuta todo
        if (!count($args)) {
            $args=['all'];
        }

        return parent::execute($args, $options);
    }

    /*
     * Procesa todo
     *
     */
    public function executeAll(array $args, array $options = array())
    {
        $this->executeHistorical($args,$options);

        $args[0]='last';
        $this->executePeriod($args,$options);
    }


    /*
     * Genera Histórico
     *
     */
    public function executeHistorical(array $args, array $options = array())
    {
        $box = new ConsoleKit\Widgets\Box($this->getConsole(), 'Generando Histórico');
        $box->write();$this->getConsole()->writeln("");

        $destFile = BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_HISTORICAL_FILE . ".csv";
        $destEntidadesFile = BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_HISTORICAL_FILE_ENTIDADES . ".csv";
        $tmpDestFile = $this->tempFile(Config::DEST_HISTORICAL_FILE);
        $tmpDestEntidadesFile = $this->tempFile(Config::DEST_HISTORICAL_FILE_ENTIDADES);
        $rebuild = isset($options['force']) || isset($options['f']) || !file_exists($destFile) || !file_exists($destEntidadesFile);
        $processedPeriods = $rebuild ? array() : $this->readHistoricalPeriods($destFile);

        $dest = fopen($tmpDestFile, 'w+');
        $destEntidades = fopen($tmpDestEntidadesFile, 'w+');

        if ($rebuild) {
            $this->writeHeaderToFile($dest,Config::$datapackage['resources']['4']['schema']['fields']);
            $this->writeHeaderToFile($destEntidades,Config::$datapackage['resources']['5']['schema']['fields']);
        } else {
            $this->copyFileToHandle($destFile, $dest);
            $this->copyFileToHandle($destEntidadesFile, $destEntidades);
        }

        foreach (glob(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "*.zip") as $source) {
            if (isset($processedPeriods[$this->periodFromSource($source)])) {
                continue;
            }

            $this->getConsole()->writeln("Procesando " . basename($source));
            $this->appendSortedSourceToFiles($source,$dest,$destEntidades);
        }

        fclose($dest);
        fclose($destEntidades);

        rename($tmpDestFile, $destFile);
        rename($tmpDestEntidadesFile, $destEntidadesFile);
        chmod($destFile, 0644);
        chmod($destEntidadesFile, 0644);
    }


    /**
     * Procesa el archivo fuente correspondiente al año especificado
     *
     * @opt year año a procesar
     *
     */
    public function executePeriod(array $args, array $options = array()){

        if (empty($args[0])) {
            $this->writeerr("Error: Year missing\n");
            die();
        }
        if (empty($args[1])) {
            $args[1] = 'last';
        }

        if ($args[0]=='last') {
            $files = glob(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "*.zip");
            $source = end($files);
        } else {
            if ($args[1]=='last') {
                $files = glob(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "{$args[0]}*.zip");
                $source = end($files);
            } else {
                $files = glob(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "{$args[0]}-{$args[1]}.zip");
                $source = end($files);
            }
        }

        $box = new ConsoleKit\Widgets\Box($this->getConsole(), "Procesando {$source}");
        $box->write();$this->getConsole()->writeln("");

        $destFile = BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_FILE . ".csv";
        $destEntidadesFile = BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_FILE_ENTIDADES . ".csv";
        $tmpDestFile = $this->tempFile(Config::DEST_FILE);
        $tmpDestEntidadesFile = $this->tempFile(Config::DEST_FILE_ENTIDADES);

        $dest = fopen($tmpDestFile, 'w+');
        $destEntidades = fopen($tmpDestEntidadesFile, 'w+');

        $this->writeHeaderToFile($dest,Config::$datapackage['resources']['2']['schema']['fields']);
        $this->writeHeaderToFile($destEntidades,Config::$datapackage['resources']['3']['schema']['fields']);

        $this->appendSortedSourceToFiles($source, $dest, $destEntidades, false);

        fclose($dest);
        fclose($destEntidades);

        rename($tmpDestFile, $destFile);
        rename($tmpDestEntidadesFile, $destEntidadesFile);
        chmod($destFile, 0644);
        chmod($destEntidadesFile, 0644);
    }



    /*
     * Graba el el cabecero con el nombre de las columnas a disco
     *
     * @param resource $file identificador del archivo
     *
     */
    private function writeHeaderToFile($file,$columns)
    {
        fputcsv($file, array_map(function($var){ return $var['name']; }, $columns));
        return;
    }

    private function appendSortedSourceToFiles($source,$file,$fileEntidades,$includeYear=true)
    {
        $tmpFile = $this->tempFile('process_rows');
        $tmpEntidadesFile = $this->tempFile('process_entidades_rows');

        $rows = fopen($tmpFile, 'w+');
        $rowsEntidades = fopen($tmpEntidadesFile, 'w+');

        $this->parseSourceToFile($source, $rows, $rowsEntidades, $includeYear);

        fclose($rows);
        fclose($rowsEntidades);

        $this->appendSortedUniqueFile($tmpFile, $file, '1,1 -k2,2');
        $this->appendSortedUniqueFile($tmpEntidadesFile, $fileEntidades, '1,1 -k2,2 -k3,3');

        unlink($tmpFile);
        unlink($tmpEntidadesFile);
    }

    private function appendSortedUniqueFile($sourceFile,$dest,$keys)
    {
        $command = 'LC_ALL=C sort -t, -u -k' . $keys . ' ' . escapeshellarg($sourceFile);
        $sorted = popen($command, 'r');

        if (!$sorted) {
            $this->writeerr("Error: could not sort {$sourceFile}\n");
            die();
        }

        while (($line = fgets($sorted)) !== false) {
            fwrite($dest, $line);
        }

        $status = pclose($sorted);
        if ($status !== 0) {
            $this->writeerr("Error: sort failed for {$sourceFile}\n");
            die();
        }
    }

    private function tempFile($prefix)
    {
        $file = tempnam(BASE_PATH . DS . Config::DATA_FOLDER, $prefix . '.');
        if ($file === false) {
            $this->writeerr("Error: could not create temporary file\n");
            die();
        }

        return $file;
    }

    private function readHistoricalPeriods($file)
    {
        $periods = array();
        $source = fopen($file, 'r');

        if (!$source) {
            return $periods;
        }

        fgetcsv($source);
        while (($row = fgetcsv($source)) !== false) {
            if (isset($row[2]) && isset($row[3])) {
                $periods[$row[2] . '-' . $row[3]] = true;
            }
        }

        fclose($source);
        return $periods;
    }

    private function copyFileToHandle($sourceFile,$dest)
    {
        $source = fopen($sourceFile, 'r');

        if (!$source) {
            $this->writeerr("Error: could not open {$sourceFile}\n");
            die();
        }

        while (!feof($source)) {
            fwrite($dest, fread($source, 1048576));
        }

        fclose($source);
    }

    private function periodFromSource($source)
    {
        list($year,$month) = explode("-",basename($source,'.zip'));
        return $year . '-' . $month;
    }



    /*
     * Procesa el archivo fuente del año especificado y lo graba a disco
     *
     * @param int $year año a procesar
     * @param int $month mes a procesar
     * @param resource $file identificador del archivo
     *
     */
    private function parseSourceToFile($source,$file,$fileEntidades,$includeYear=true){

        list($year,$month) = explode("-",basename($source,'.zip'));

        $zip = new ZipArchive;
        $zip->open($source);

        for( $i = 0; $i < $zip->numFiles; $i++ ) {
            if (strstr($zip->statIndex($i)['name'], 'TRAM') ||
                strstr($zip->statIndex($i)['name'], 'Tramero') ||
                strstr($zip->statIndex($i)['name'], 'P01t')) {
                $zippedSourceFileName = $zip->statIndex($i)['name'];
                break;
            }
        }

        if (!isset($zippedSourceFileName)){
            $this->writeerr("Error: TRAM* source not found in {$source}\n");
            die();
        }

        // double ZIP
        if (substr($zippedSourceFileName, -4) == ".zip") {
            $zip->extractTo('/tmp', array($zippedSourceFileName));
            $zip2 = new ZipArchive;
            $zip2->open('/tmp/' . $zippedSourceFileName);

            for( $i = 0; $i < $zip2->numFiles; $i++ ) {
                if (strstr($zip2->statIndex($i)['name'], 'TRAM') || 
                    strstr($zip->statIndex($i)['name'], 'Tramero')) {
                    $zippedSourceFileName2 = $zip2->statIndex($i)['name'];
                    break;
                }
            }

            if (!isset($zippedSourceFileName2)){
                $this->writeerr("Error: TRAM* source not found in {$zippedSourceFileName}\n");
                die();
            }

            $zippedSource = $zip2->getStream($zippedSourceFileName2);
        } else {
            $zippedSource = $zip->getStream($zippedSourceFileName);
        }


        while (($line = fgets($zippedSource)) !== false) {
            $line = iconv("windows-1252", "UTF-8", $line);

            $codigo_postal = mb_substr($line,42,5);
            $municipio_id = mb_substr($line,0,5);

            $codigo_unidad_poblacional = mb_substr($line,13,7);
            //$nombre_entidad_colectiva = $this->titleCase(trim(substr($line,85,25)));
            $nombre_entidad_singular = $this->titleCase(trim(mb_substr($line,110,25)));
            $nombre_nucleo = $this->titleCase(trim(mb_substr($line,135,25)));

            if ($includeYear) {
                fputcsv($file, [$codigo_postal, $municipio_id, $year,$month]);
                fputcsv($fileEntidades, [$codigo_postal, $municipio_id, $codigo_unidad_poblacional,$nombre_entidad_singular,$nombre_nucleo ,$year,$month]);
            } else {
                fputcsv($file, [$codigo_postal, $municipio_id]);
                fputcsv($fileEntidades, [$codigo_postal, $municipio_id, $codigo_unidad_poblacional,$nombre_entidad_singular,$nombre_nucleo]);

                //echo $codigo_postal . "\r\n";
            }

        }

    }

    /**
     * @param $string Cadena de texto a convertir
     * @param array $delimiters Carácteres delimitadores
     * @param array $exceptions Palabras a las que no se les cambia la capitalización
     * @return string
     */
    private function titleCase($string, $delimiters = array(), $exceptions = array()) {

        if (empty($delimiters)) {
            $delimiters = array(" ", "-", "/",",","'");
        }

        if (empty($exceptions)) {
            $exceptions = array("de", "del", "la","II",'III','IV','XIII','XXIII');
        }

        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");

        foreach ($delimiters as $dlnr => $delimiter){
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word){

                $wordLowerCase = strtolower($word);
                $wordUpperCase = strtoupper($word);
                if (in_array($wordLowerCase, $exceptions)){  // check exceptions list for any words that should be in lower case
                    $word = $wordLowerCase;
                } else if (in_array($wordUpperCase, $exceptions)){  // check exceptions list for any words that should be in upper case
                    $word = $wordUpperCase;
                }
                elseif (!in_array($word, $exceptions) ){
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
        }//foreach
        return $string;
    }

}
