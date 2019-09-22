<?php

require_once('Config.php');

/**
 * Descarga los archivos fuente y los graba a disco
 *
 */
class DownloadCommand extends ConsoleKit\Command
{
    /**
     * Overriding para que invoque all por defecto.
     */
    public function execute(array $args, array $options = array())
    {
        //si se invoca sin su comando, ejecuta todo
        if (!count($args)) {
            $args=['all'];
        }

        return parent::execute($args, $options);
    }

    /**
     * Descarga todos los años
     *
     * @opt force fuerza la descarga de los ficheros fuente aunque ya existan
     *
     */
    public function executeAll(array $args, array $options = array())
    {
        for ($year = Config::YEAR_START;$year <= date('Y');$year++){
            $this->download($year,1,$options); // Enero
            $this->download($year,7,$options); // Julio
        }

    }

    /**
     * Descarga solo el año especificado
     *
     * @arg args parámetros CLI
     * @opt force fuerza la descarga de los ficheros fuente aunque ya existan
     */
    public function executeYear(array $args, array $options = array())
    {
        if (empty($args[0])) {
            $this->writeerr("Error: Year missing\n");
            die();
        }
        if (empty($args[1])) {
            $this->writeerr("Error: Month missing\n");
            die();
        }
        $this->download($args['0'],$args['1'],$options);
    }

    /**
     * Descarga archivo fuente especificado por año
     *
     * @param year año a descargar
     * @param month mes a descargar
     * @opt force fuerza la descarga de los ficheros fuente aunque ya existan
     */
    private function download($year, $month, $options=array())
    {
        $url=sprintf(Config::URL,$month,$year);
        $fileName = sprintf(Config::SOURCE_FILE, $month,$year);

        $destFileNameFull = BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . "$year-0{$month}.zip";

        if (!file_exists($destFileNameFull) || isset($options['force']) || isset($options['f'])){
            if (!($remote = @fopen ($url, "rb"))){ //404 Not found
                return;
            }
            file_put_contents($destFileNameFull, $remote);
            $box = new ConsoleKit\Widgets\Box($this->getConsole(), "Descargando - {$year} - {$month}");
            $box->write();$this->getConsole()->writeln("");
        }

    }
}