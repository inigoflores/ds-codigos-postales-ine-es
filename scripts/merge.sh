#!/usr/bin/env bash


curl https://raw.githubusercontent.com/codeforspain/ds-organizacion-administrativa/master/data/municipios.csv | csvcut -c 'municipio_id,nombre' |csvjoin -c "municipio_id" ../data/codigos_postales_municipios.csv - |  csvcut -C "municipio_id,nombre_entidad_singular" > ../data/codigos_postales_municipios_join.csv

