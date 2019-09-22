#!/usr/bin/env bash

curl https://raw.githubusercontent.com/codeforspain/ds-organizacion-administrativa/master/data/municipios.csv \
	| csvcut -c 'municipio_id,nombre' \
	| csvjoin -c "municipio_id" ../data/codigos_postales_municipios.csv - \
	| csvcut -c "codigo_postal,nombre_entidad_singular,municipio_id,nombre" \
	> ../data/codigos_postales_municipios_join.csv

