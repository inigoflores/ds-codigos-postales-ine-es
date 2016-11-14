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
        //si se invoca sin sucomando, ejecuta todo
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
            $this->downloadYear($year,$options);
        }

    }

    /**
     * Descarga solo el año especificado
     *
     * @arg year año a descargar
     * @opt force fuerza la descarga de los ficheros fuente aunque ya existan
     */
    public function executeYear(array $args, array $options = array())
    {
        if (empty($args[0])) {
            $this->writeerr("Error: Year missing\n");
            die();
        }

        $this->downloadYear($args['0'],$options);
    }

    /**
     * Descarga archivo fuente especificado por año
     *
     * @param year año a descargar
     * @opt force fuerza la descarga de los ficheros fuente aunque ya existan
     */
    private function downloadYear($year,$options=array())
    {
        $url=sprintf(Config::URL,$year);
        $fileName = sprintf(Config::SOURCE_FILE, $year);

        $destFileNameFull = BASE_PATH . DS . Config::ARCHIVE_FOLDER . DS . $fileName;

        if (!file_exists($destFileNameFull) || isset($options['force']) || isset($options['f'])){
            file_put_contents($destFileNameFull, fopen($url, 'r'));
            $box = new ConsoleKit\Widgets\Box($this->getConsole(), "Descargando Año - {$year}");
            $box->write();$this->getConsole()->writeln("");
        }

    }
}