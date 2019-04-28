# ds-codigos-postales-ine-es

Dataset que proporciona un listado de todos los códigos postales de España asociados al código INE del municipio al que pertenecen.

Usa como fuente el Callejero del Censo Electoral (INE).

## Codigos Postales por Municipio
- Fuente: [Callejero del Censo Electoral (INE)](http://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1254735624326&p=1254735624326&pagename=ProductosYServicios%2FPYSLayout)
- URL: `http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_[MM][YYYY].zip` donde `MM` es el mes (01 ó 07) y la cadena `YYYY` es el último año
- Tipo: Texto de ancho fijo comprimido (.zip)
- Datos procesados: [/data/codigos_postales_municipios.csv](data/codigos_postales_municipios.csv)


### Formato de los datos


Ejemplo en CSV:

| codigo_postal | municipio_id | nombre_entidad_singular   |
|---------------|--------------|---------------------------|
| 28100         | 28006        | ALCOBENDAS                |
| 28108         | 28006        | ALCOBENDAS                |
| 28109         | 28006        | ALCOBENDAS                |
| 28110         | 28009        | "DEHESA NUEVA"            |
| 28120         | 28045        | "PUEBLAS (LAS)"           |
| 28124         | 28124        | "ROBLEDILLO DE LA JARA"   |
| 28130         | 28162        | MIRAVAL                   |
| 28140         | 28059        | ADOBERAS                  |
| 28150         | 28164        | "MIRADOR (EL)"            |
| 28160         | 28145        | "MORALEJA (LA)"           |
| 28170         | 28163        | "COTO DE SAN BENITO"      |
| 28180         | 28151        | "TOMILLARES (LOS)"        |
| 28189         | 28153        | "SOTO (EL)"               |
| 28190         | 28118        | "PUEBLA DE LA SIERRA"     |
| 28191         | 28117        | "PRADENA DEL RINCON"      |
| 28192         | 28021        | "DEHESA BOYAL"            |
| 28193         | 28039        | "CERCADOS (LOS)"          |
| 28194         | 28124        | "VILLAR (EL)"             |
| 28195         | 28902        | "SERRADA DE LA FUENTE"    |
| 28196         | 28902        | "PRESA DE PUENTES VIEJAS" |
| 28200         | 28131        | "JURISDICCION (LA)"       |
| 28210         | 28160        | VALDEMORILLO              |
| 28211         | 28054        | PERALEJO                  |
| 28212         | 28095        | "BARRANCOS (LOS)"         |


## Codigos Postales por Municipio (Histórico)
- Fuente: [Callejero del Censo Electoral (INE)](http://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1254735624326&p=1254735624326&pagename=ProductosYServicios%2FPYSLayout)
- URL: `http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_[MM][YYYY].zip` donde `MM` es el mes (01 ó 07) y la cadena `YYYY` es el año, (desde 2013 hasta la actualidad))
- Tipo: Texto de ancho fijo comprimido (.zip)
- Datos procesados: [/data/codigos_postales_municipios_historical.csv](data/codigos_postales_municipios_historical.csv)



### Formato de los datos


Ejemplo en CSV:

| codigo_postal | municipio_id | nombre_entidad_singular | year | month |
|---------------|--------------|-------------------------|------|-------|
| 29610         | 29076        | OJEN                    | 2013 |    07 |
| 29610         | 29076        | OJEN                    | 2014 |    01 |
| 29612         | 29076        | OJEN                    | 2014 |    01 |


En el ejemplo se aprecia como en 2014 aparece un nuevo código postal para el municipio de Ojén.



## Script

El script se puede encontrar en [/scripts/](/scripts/).


## Merge con ds-organizacion-administrativa

Para obtener el nombre de municipio asociado al codigo INE se puede hacer un merge con `ds-organizacion-administrativa/ds-oa-municipios` mediante `csvjoin`:

    $ `curl https://raw.githubusercontent.com/codeforspain/ds-organizacion-administrativa/master/data/municipios.csv |
         csvcut -c 'municipio_id,nombre' |csvjoin -c "municipio_id"  codigos_postales_municipios.csv - |
         csvcut -C "municipio_id,nombre_entidad_singular" >codigos_postales_municipios_join.csv`

Este comando devuelve [codigos_postales_municipios_join.csv](data/codigos_postales_municipios_join.csv). Ejemplo:


| codigo_postal | nombre_entidad_singular | municipio_id | nombre                     |
|---------------|-------------------------|--------------|----------------------------|
| 28100         | ALCOBENDAS              | 28006        | Alcobendas                 |
| 28108         | ALCOBENDAS              | 28006        | Alcobendas                 |
| 28109         | ALCOBENDAS              | 28006        | Alcobendas                 |
| 28110         | DEHESA NUEVA            | 28009        | Algete                     |
| 28120         | PUEBLAS (LAS)           | 28045        | Colmenar Viejo             |
| 28124         | ROBLEDILLO DE LA JARA   | 28124        | Robledillo de la Jara      |
| 28130         | MIRAVAL                 | 28162        | Valdeolmos-Alalpardo       |
| 28140         | ADOBERAS                | 28059        | Fuente el Saz de Jarama    |
| 28150         | MIRADOR (EL)            | 28164        | Valdetorres de Jarama      |
| 28160         | MORALEJA (LA)           | 28145        | Talamanca de Jarama        |
| 28170         | COTO DE SAN BENITO      | 28163        | Valdepiélagos              |
| 28180         | TOMILLARES (LOS)        | 28151        | Torrelaguna                |
| 28189         | SOTO (EL)               | 28153        | Torremocha de Jarama       |
| 28190         | PUEBLA DE LA SIERRA     | 28118        | Puebla de la Sierra        |
| 28191         | PRADENA DEL RINCON      | 28117        | Prádena del Rincón         |
| 28192         | DEHESA BOYAL            | 28021        | "Berrueco, El"             |
| 28193         | CERCADOS (LOS)          | 28039        | Cervera de Buitrago        |
| 28194         | VILLAR (EL)             | 28124        | Robledillo de la Jara      |
| 28195         | SERRADA DE LA FUENTE    | 28902        | Puentes Viejas             |
| 28196         | PRESA DE PUENTES VIEJAS | 28902        | Puentes Viejas             |
| 28200         | JURISDICCION (LA)       | 28131        | San Lorenzo de El Escorial |
| 28210         | VALDEMORILLO            | 28160        | Valdemorillo               |
| 28211         | PERALEJO                | 28054        | "Escorial, El"             |
| 28212         | BARRANCOS (LOS)         | 28095        | Navalagamella              |



