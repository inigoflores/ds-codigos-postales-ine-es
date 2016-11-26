<?php

require_once('Config.php');

use ConsoleKit\Widgets\ProgressBar;

/**
 * Actualiza datapackage.json
 *
 * @opt nojson no incluye los recursos .json
 */
class UpdateCommand extends ConsoleKit\Command
{

    public function execute(array $args, array $options = array())
    {
        $datapackageNew = Config::$datapackage;
        $datapackageNew['last_updated']=date('Y-m-d ');

        $box = new ConsoleKit\Widgets\Box($this->getConsole(), 'Actualizando datapackage.json');
        $box->write();$this->getConsole()->writeln("");

        // Comprobobamos si hay que omitir JSON
        if (!isset($options['nojson']) && !isset($options['n'])){
            foreach ( Config::$datapackage['resources'] as $resource){
                $resource['format'] = 'json';
                $resource['path'] = array_shift(explode('.',$resource['path'])) . ".json";
                $datapackageNew['resources'][]=$resource;
            }
        }


        // Actualizamos versión
        if (file_exists(BASE_PATH . DS . "datapackage.json")) {
            $datapackageOld = json_decode(file_get_contents(BASE_PATH . DS . "datapackage.json"));
            if (!empty($datapackageOld->version)) {
                $semver = explode(".", $datapackageOld->version);
                if (sizeof($semver)==3) $datapackageNew['version'] = implode(".", [$semver[0],$semver[1],++$semver[2]]);
            } else {
                $datapackageNew['version']="0.0.1";
            }
        }


        file_put_contents(BASE_PATH . DS . "datapackage.json", json_encode($datapackageNew,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }


}