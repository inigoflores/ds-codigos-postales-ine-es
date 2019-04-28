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

        $dest = fopen(BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_HISTORICAL_FILE . ".csv", 'w+');
        $this->writeHeaderToFile($dest,Config::$datapackage['resources']['1']['schema']['fields']);

        foreach (glob(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "*.zip") as $source) {
            $this->parseSourceToFile($source,$dest);
        }


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

        $lastMonth = (date('m') >= 7) ? 7 : 1;
        $year = ($args[0]=='last') ? date('Y') : $args[0];
        $month = ($args[0]=='last') ? $lastMonth : $args[1];
        $source = sprintf(Config::SOURCE_FILE, $month, $year);

        $box = new ConsoleKit\Widgets\Box($this->getConsole(), "Generando - {$year}-{$month}");
        $box->write();$this->getConsole()->writeln("");

        $file = fopen(BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_FILE . ".csv", 'w+');

        $this->writeHeaderToFile($file,Config::$datapackage['resources']['0']['schema']['fields']);
        $this->parseSourceToFile(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . $source, $file, false);
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



    /*
     * Procesa el archivo fuente del año especificado y lo graba a disco
     *
     * @param int $year año a procesar
     * @param int $month mes a procesar
     * @param resource $file identificador del archivo
     *
     */
    private function parseSourceToFile($source,$file,$includeYear=true){

        //$fileName = BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . sprintf(Config::SOURCE_FILE, $year);

        $temp = explode("caj_esp_",basename($source))[1];
        $month = substr($temp,0,2);
        $year = substr($temp,2,4);

        $zip = new ZipArchive;
        $zip->open($source);

        for( $i = 0; $i < $zip->numFiles; $i++ ) {
            if (strstr($zip->statIndex($i)['name'], 'TRAM')) {
                $zippedSourceFileName = $zip->statIndex($i)['name'];
                break;
            }
        }

        if (!isset($zippedSourceFileName)){
            $this->writeerr("Error: TRAM* source not found in {$source}\n");
            die();
        }

        $zippedSource = $zip->getStream($zippedSourceFileName);

        while (($line = fgets($zippedSource)) !== false) {
            $codigo_postal = substr($line,42,5);
            $municipio_id = substr($line,0,5);
            $nombre_entidad_singular = iconv("windows-1252", "UTF-8", trim(substr($line,110,25)));
            $output[$codigo_postal][$municipio_id] = compact('codigo_postal','municipio_id','nombre_entidad_singular');

            if ($includeYear) {
                $output[$codigo_postal][$municipio_id]['year'] = $year;
                $output[$codigo_postal][$municipio_id]['month'] = $month;
            }
            $i++;
        }

        ksort($output);

        foreach ($output as $codigos_postales){
            foreach ($codigos_postales as $codigo_postal){
                fputcsv($file, $codigo_postal);
            }
        }

    }

}
