<?php
/**
 * Si no existen, descarga los archivos fuente y los graba a disco
 *
 * @arg source fuente a procesar (OPCIONAL). Puede ser "municipios", "provincias", "autonomias" o "islas". Si no se especifica  procesa todo.
 * @opt force fuerza la descarga de los ficheros fuente aunque ya existan
 *
 */

use ConsoleKit\Widgets\ProgressBar;
use ConsoleKit\Widgets\Box;

class ConvertToJsonCommand extends ConsoleKit\Command
{
    public function execute(array $args, array $options = array())
    {
        $box = new ConsoleKit\Widgets\Box($this->getConsole(), 'Convirtiendo a JSON');
        $box->write();$this->getConsole()->writeln("");

        $files = glob(BASE_PATH . DS . Config::DATA_FOLDER . DS ."*.csv");
        $progress = new ProgressBar($this->getConsole(), sizeof($files));

        foreach ($files as $csvFile) {
            $outputFile = BASE_PATH . DS . Config::DATA_FOLDER . DS . basename($csvFile,".csv") . ".json";
            exec("csvjson $csvFile > $outputFile");
            $progress->incr();
        }

        $progress->stop();
    }

}