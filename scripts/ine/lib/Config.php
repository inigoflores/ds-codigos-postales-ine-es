<?php

class Config
{

    const URL = "http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_01%d.zip";

    const YEAR_START = 2013; //existe desde 2012, pero usa doble compresión. To do.
    const DATA_FOLDER ="data";
    const ARCHIVE_FOLDER = "archive";
    const SOURCE_FILE = "caj_esp_01%d.zip";
    const DEST_FILE = "codigos_postales_municipios";
    const DEST_HISTORICAL_FILE = "codigos_postales_municipios_historical";

    //TRAMOS-NAL.F151231

    static $datapackage = [
        "name" => "ds-codigos-postales",
        "title" => "Dataset de Códigos Postales",
        "descriptions" => "Codigos postales de los municipios españoles",
        "licenses" => [
            [
                "type" => "odc-pddl",
                "url" => "http://opendatacommons.org/licenses/pddl/"
            ]
        ],
        "author" => [
            "name" => "Code for Spain",
            "web" => "http://www.codeforspain.org"
        ],
        "keywords" => [ "Codigo Postal"],

        "sources" => [
             [
                "name" => "Callejero del Censo Electoral (INE)",
                "web" => "http://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1254735624326&p=1254735624326&pagename=ProductosYServicios%2FPYSLayout"
             ]
        ],
        "resources" => [
            [
                "name" => "ds_codigos_postales_municipios",
                "title"=> "Codigos postales por municipio",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipios.csv",
                "schema"=> [
                    "fields"=> [
                        [
                            "name" => "codigo_postal",
                            "type" => "number",
                            "description" => "Código Postal",
                            "pattern" => "[0-9]{5}"
                        ],
                        [
                            "name" => "municipio_id",
                            "type" => "number",
                            "description" => "Código INE del municipio",
                            "pattern" => "[0-9]{5}"
                        ],
                        [
                            "name" => "nombre_entidad_singular",
                            "type" => "string",
                            "description" => "Nombre entidad singular",
                        ],

                    ]
                ]
            ],
            [
                "name" => "ds_codigos_postales_municipios_historical",
                "title"=> "Histórico de codigos postales por municipio (desde 2012)",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipios_historical.csv",
                "schema"=> [
                    "fields"=> [
                        [
                            "name" => "codigo_postal",
                            "type" => "number",
                            "description" => "Código Postal",
                            "pattern" => "[0-9]{5}"
                        ],
                        [
                            "name" => "municipio_id",
                            "type" => "number",
                            "description" => "Código INE del municipio",
                            "pattern" => "[0-9]{5}"
                        ],
                        [
                            "name" => "nombre_entidad_singular",
                            "type" => "string",
                            "description" => "Nombre entidad singular",
                        ],
                        [
                            "name" => "year",
                            "type" => "number",
                            "description" => "Año del dato",
                        ],
                    ]
                ]
            ]
       ]
    ];


}