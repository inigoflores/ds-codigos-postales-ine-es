<?php

class Config
{

    const URL = "http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_0%d%d.zip";

    const YEAR_START = 2012;
    const DATA_FOLDER ="data";
    const ARCHIVE_FOLDER = "archive";
    const SOURCE_FILE = "caj_esp_0%d%d.zip";
    const DEST_FILE = "codigos_postales_municipiosid";
    const DEST_FILE_ENTIDADES = "codigos_postales_municipiosid_entidades";
    const DEST_HISTORICAL_FILE = "codigos_postales_municipiosid_historical";
    const DEST_HISTORICAL_FILE_ENTIDADES = "codigos_postales_municipiosid_entidades_historical";

    //TRAMOS-NAL.F151231

    static $datapackage = [
        "name" => "ds-codigos-postales-ine-es",
        "title" => "Dataset de Códigos Postales (Callejero INE)",
        "descriptions" => "Codigos postales de los municipios españoles obtenidos del Callejero INE",
        "licenses" => [
            [
                "type" => "odc-pddl",
                "url" => "http://opendatacommons.org/licenses/pddl/"
            ]
        ],
        "author" => [
            "name" => "Iñigo Flores"
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
                "title"=> "Codigos postales y municipios asociados",
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
                            "name" => "municipio_nombre",
                            "type" => "string",
                            "description" => "Nombre del municipio",
                        ],
                    ]
                ]
            ],
            [
                "name" => "ds_codigos_postales_municipios_entidades",
                "title"=> "Códigos postales y municipios y entidades singulares asociados",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipios_entidades.csv",
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
                            "name" => "entidad_singular_nombre",
                            "type" => "string",
                            "description" => "Nombre entidad singular",
                        ],
                        [
                            "name" => "municipio_nombre",
                            "type" => "string",
                            "description" => "Nombre del municipio",
                        ],

                    ]
                ]
            ],
            [
                "name" => "ds_codigos_postales_municipiosid",
                "title"=> "Códigos postales y códigos INE de los municipios asociados",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipiosid.csv",
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
                    ]
                ]
            ],
            [
                "name" => "ds_codigos_postales_municipiosid_entidades",
                "title"=> "Códigos postales y códigos INE de municipios y entidades singulares asociados",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipiosid_entidades.csv",
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
                            "name" => "entidad_singular_nombre",
                            "type" => "string",
                            "description" => "Nombre entidad singular",
                        ],

                    ]
                ]
            ],
            [
                "name" => "ds_codigos_postales_municipiosid_historical",
                "title"=> "Histórico de códigos postales y códigos INE de municipios asociados (desde 2013)",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipiosid_historical.csv",
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
                            "name" => "year",
                            "type" => "number",
                            "description" => "Año del dato",
                        ],
                        [
                            "name" => "month",
                            "type" => "number",
                            "description" => "Mes del dato",
                        ]
                    ]
                ]
            ],
            [
                "name" => "ds_codigos_postales_municipiosid_entidades_historical",
                "title"=> "Histórico de códigos postales y códigos INE de municipios y entidades singulares asociados (desde 2013)",
                "format"=> "csv",
                "path"=> "data/codigos_postales_municipiosid_historical.csv",
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
                            "name" => "entidad_singular_nombre",
                            "type" => "string",
                            "description" => "Nombre entidad singular",
                        ],
                        [
                            "name" => "year",
                            "type" => "number",
                            "description" => "Año del dato",
                        ],
                        [
                            "name" => "month",
                            "type" => "number",
                            "description" => "Mes del dato",
                        ]

                    ]
                ]
            ]
       ]
    ];


}
