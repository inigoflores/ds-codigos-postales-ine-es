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
        $destEntidades = fopen(BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_HISTORICAL_FILE_ENTIDADES . ".csv", 'w+');

        $this->writeHeaderToFile($dest,Config::$datapackage['resources']['2']['schema']['fields']);
        $this->writeHeaderToFile($destEntidades,Config::$datapackage['resources']['3']['schema']['fields']);

        foreach (glob(BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "*.zip") as $source) {
            $this->parseSourceToFile($source,$dest,$destEntidades);
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

        $dest = fopen(BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_FILE . ".csv", 'w+');
        $destEntidades = fopen(BASE_PATH . DS . Config::DATA_FOLDER . DS . Config::DEST_FILE_ENTIDADES . ".csv", 'w+');

        $this->writeHeaderToFile($dest,Config::$datapackage['resources']['0']['schema']['fields']);
        $this->writeHeaderToFile($destEntidades,Config::$datapackage['resources']['1']['schema']['fields']);

        $this->parseSourceToFile($source, $dest, $destEntidades, false);
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
    private function parseSourceToFile($source,$file,$fileEntidades,$includeYear=true){

        list($year,$month) = explode("-",basename($source,'.zip'))[1];

        list($year,$month) = explode("-",basename($source,'.zip'));


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

        // double ZIP
        if (substr($zippedSourceFileName, -4) == ".zip") {
            $zip->extractTo('/tmp', array($zippedSourceFileName));
            $zip2 = new ZipArchive;
            $zip2->open('/tmp/' . $zippedSourceFileName);

            for( $i = 0; $i < $zip2->numFiles; $i++ ) {
                if (strstr($zip2->statIndex($i)['name'], 'TRAM')) {
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
            $codigo_postal = substr($line,42,5);
            $municipio_id = substr($line,0,5);
            $nombre_entidad_singular = iconv("windows-1252", "UTF-8", trim(substr($line,110,25)));

            if ($includeYear) {
                $items[$codigo_postal.$municipio_id.$year.$month] = compact('codigo_postal','municipio_id', 'year', 'month');
                $itemsEntidades[$codigo_postal.$municipio_id.$nombre_entidad_singular.$year.$month] = compact('codigo_postal','municipio_id','nombre_entidad_singular','year','month');

            } else {
                $items[$codigo_postal.$municipio_id] = compact('codigo_postal','municipio_id');
                $itemsEntidades[$codigo_postal.$municipio_id.$nombre_entidad_singular] = compact('codigo_postal','municipio_id','nombre_entidad_singular');

            }

            $i++;
        }

        ksort($items);
        ksort($itemsEntidades);

        foreach ($items as $item){
            fputcsv($file, $item);
        }

        foreach ($itemsEntidades as $item){
            fputcsv($fileEntidades, $item);
        }

    }

}
