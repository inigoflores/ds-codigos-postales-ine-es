<?php

require_once('Config.php');

use ConsoleKit\Widgets\ProgressBar;

/**
 * Hace un merge con ds-organizacion-administrativa
 *
 */
class MergeCommand extends ConsoleKit\Command
{

    public function execute(array $args, array $options = array())
    {
        $box = new ConsoleKit\Widgets\Box($this->getConsole(), 'Haciendo merge con ds-organizacion-administrativa');
        $box->write();$this->getConsole()->writeln("");

        shell_exec('
            curl https://raw.githubusercontent.com/codeforspain/ds-organizacion-administrativa/master/data/municipios.csv \
                | csvcut -c "municipio_id,nombre" \
                | csvjoin -I -c "municipio_id" ../data/codigos_postales_municipiosid.csv - \
                | csvcut -c "codigo_postal,municipio_id,nombre" \
                > ../data/codigos_postales_municipios.csv
        ');

        shell_exec("
            sed -i 's/nombre/municipio_nombre/g' ../data/codigos_postales_municipios.csv
        ");

        shell_exec('
             curl https://raw.githubusercontent.com/codeforspain/ds-organizacion-administrativa/master/data/municipios.csv \
	            | csvcut -c "municipio_id,nombre" \
	            | csvjoin --snifflimit 0 -I -c "municipio_id" ../data/codigos_postales_municipiosid_entidades.csv - \
	            | csvcut -c "codigo_postal,municipio_id,nombre,codigo_unidad_poblacional,entidad_singular_nombre,nucleo_nombre" \
	            > ../data/codigos_postales_municipios_entidades.csv
        ');

        shell_exec("
            sed -i 's/,nombre/,municipio_nombre/g' ../data/codigos_postales_municipios_entidades.csv
        ");

    }
}
