# ds-codigos-postales-ine-es

Dataset que proporciona un listado de todos los códigos postales de España asociados a los municipios y unidades poblacionales.

Usa como fuente el Callejero del Censo Electoral (INE).



## Codigos Postales y Municipios Asociados
- Fuente: [Callejero del Censo Electoral (INE)](http://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1254735624326&p=1254735624326&pagename=ProductosYServicios%2FPYSLayout)
- URL: `http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_[MM][YYYY].zip` donde `MM` es el mes (01 ó 07) y la cadena `YYYY` es el último año
- Tipo: Texto de ancho fijo comprimido (.zip)
- Datos procesados: [/data/codigos_postales_municipios.csv](data/codigos_postales_municipios.csv)


### Formato de los datos


Ejemplo en CSV:

|codigo_postal|municipio_id|municipio_nombre|
|-------------|------------|----------------|
|29601        |29069       |Marbella        |
|29602        |29069       |Marbella        |
|29603        |29069       |Marbella        |
|29604        |29069       |Marbella        |
|29610        |29076       |Ojén            |
|29611        |29061       |Istán           |
|29612        |29076       |Ojén            |
|29620        |29067       |Málaga          |
|29620        |29901       |Torremolinos    |
|29630        |29025       |Benalmádena     |
|29631        |29025       |Benalmádena     |
|29639        |29025       |Benalmádena     |
|29640        |29054       |Fuengirola      |
|29649        |29070       |Mijas           |



## Codigos Postales y Unidades Poblacionales Asociadas
- Fuente: [Callejero del Censo Electoral (INE)](http://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1254735624326&p=1254735624326&pagename=ProductosYServicios%2FPYSLayout)
- URL: `http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_[MM][YYYY].zip` donde `MM` es el mes (01 ó 07) y la cadena `YYYY` es el último año
- Tipo: Texto de ancho fijo comprimido (.zip)
- Datos procesados: [/data/codigos_postales_municipios_entidades.csv](data/codigos_postales_municipios_entidades.csv)


### Formato de los datos


Ejemplo en CSV:

|codigo_postal|municipio_id|municipio_nombre|codigo_unidad_poblacional|entidad_singular_nombre  |nucleo_nombre            |
|-------------|------------|----------------|-------------------------|-------------------------|-------------------------|
|29601        |29069       |Marbella        |0009401                  |Marbella                 |Marbella                 |
|29602        |29069       |Marbella        |0009401                  |Marbella                 |Marbella                 |
|29602        |29069       |Marbella        |0009404                  |Marbella                 |Monteros (Los)           |
|29603        |29069       |Marbella        |0009401                  |Marbella                 |Marbella                 |
|29603        |29069       |Marbella        |0009404                  |Marbella                 |Monteros (Los)           |
|29603        |29069       |Marbella        |0009405                  |Marbella                 |Ricmar                   |
|29603        |29069       |Marbella        |0009406                  |Marbella                 |Rio Real                 |
|29603        |29069       |Marbella        |0010801                  |Nueva Andalucia          |Nueva Andalucia          |
|29604        |29069       |Marbella        |0009402                  |Marbella                 |Artola                   |
|29604        |29069       |Marbella        |0009403                  |Marbella                 |Elviria                  |
|29604        |29069       |Marbella        |0009405                  |Marbella                 |Ricmar                   |
|29604        |29069       |Marbella        |0009407                  |Marbella                 |Rosario (El)             |
|29604        |29069       |Marbella        |0009499                  |Marbella                 |*diseminado*             |
|29610        |29076       |Ojén            |0001701                  |Ojen                     |Ojen                     |
|29610        |29076       |Ojén            |0001702                  |Ojen                     |Mairena (La)             |
|29610        |29076       |Ojén            |0001799                  |Ojen                     |*diseminado*             |
|29611        |29061       |Istán           |0001701                  |Istan                    |Istan                    |
|29611        |29061       |Istán           |0001702                  |Istan                    |Balcones del Lago        |
|29611        |29061       |Istán           |0001703                  |Istan                    |Cerros del Lago          |
|29611        |29061       |Istán           |0001799                  |Istan                    |*diseminado*             |
|29612        |29076       |Ojén            |0001702                  |Ojen                     |Mairena (La)             |
|29620        |29067       |Málaga          |0002203                  |Churriana                |Cortijo de Maza-El Olivar|
|29620        |29067       |Málaga          |0002299                  |Churriana                |*diseminado*             |
|29620        |29901       |Torremolinos    |0001706                  |Torremolinos             |Torremolinos             |
|29620        |29901       |Torremolinos    |0001799                  |Torremolinos             |*diseminado*             |
|29630        |29025       |Benalmádena     |0001701                  |Arroyo de la Miel-Benalma|Arroyo de la Miel-Benalma|
|29630        |29025       |Benalmádena     |0001702                  |Arroyo de la Miel-Benalma|Torrequebrada            |
|29630        |29025       |Benalmádena     |0002202                  |Benalmadena              |Capellania (La)          |
|29630        |29025       |Benalmádena     |0002204                  |Benalmadena              |Perla-Torremuelle (La)   |
|29631        |29025       |Benalmádena     |0001701                  |Arroyo de la Miel-Benalma|Arroyo de la Miel-Benalma|
|29639        |29025       |Benalmádena     |0002201                  |Benalmadena              |Benalmadena              |
|29639        |29025       |Benalmádena     |0002202                  |Benalmadena              |Capellania (La)          |
|29639        |29025       |Benalmádena     |0002203                  |Benalmadena              |Carvajal                 |
|29639        |29025       |Benalmádena     |0002204                  |Benalmadena              |Perla-Torremuelle (La)   |
|29639        |29025       |Benalmádena     |0002205                  |Benalmadena              |Santana                  |
|29639        |29025       |Benalmádena     |0002206                  |Benalmadena              |Sierrezuela (La)         |
|29639        |29025       |Benalmádena     |0002299                  |Benalmadena              |*diseminado*             |
|29640        |29054       |Fuengirola      |0001701                  |Fuengirola               |Fuengirola               |
|29649        |29070       |Mijas           |0006999                  |Entrerrios               |*diseminado*             |
|29649        |29070       |Mijas           |0007504                  |Lagunas (Las)            |Mijas Golf               |
|29649        |29070       |Mijas           |0016701                  |Calahonda-Chaparral      |Cala (La)                |
|29649        |29070       |Mijas           |0016702                  |Calahonda-Chaparral      |Cerros del Aguila        |
|29649        |29070       |Mijas           |0016703                  |Calahonda-Chaparral      |Chaparral                |
|29649        |29070       |Mijas           |0016704                  |Calahonda-Chaparral      |Sitio de Calahonda       |
|29649        |29070       |Mijas           |0016799                  |Calahonda-Chaparral      |*diseminado*             |




## Codigos Postales y Municipios Asociados (Histórico)
- Fuente: [Callejero del Censo Electoral (INE)](https://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1259952026632&p=1259952026632&pagename=ProductosYServicios%2FPYSLayout)
- URL: `http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_[MM][YYYY].zip` donde `MM` es el mes (01 ó 07) y la cadena `YYYY` es el año, (desde 2013 hasta la actualidad))
- Tipo: Texto de ancho fijo comprimido (.zip)
- Datos procesados: [/data/codigos_postales_municipiosid_historical.csv](data/codigos_postales_municipiosid_historical.csv)

En este caso no se incluye el nombre del municipio. 

### Formato de los datos


Ejemplo en CSV:

|codigo_postal|municipio_id|year        |month|
|-------------|------------|------------|-----|
|29610        |29076       |2013        |01   |
|29620        |29076       |2013        |01   |
|29610        |29076       |2013        |07   |
|29612        |29076       |2013        |07   |


En el ejemplo se aprecia como en julio de 2013 desaparece el código postal 29620 y aparece el código postal 29612 para el municipio 29076.


## Codigos Postales y Unidades Poblacionales Asociadas
- Fuente: [Callejero del Censo Electoral (INE)](https://www.ine.es/ss/Satellite?L=es_ES&c=Page&cid=1259952026632&p=1259952026632&pagename=ProductosYServicios%2FPYSLayout)
- URL: `http://www.ine.es/prodyser/callejero/caj_esp/caj_esp_[MM][YYYY].zip` donde `MM` es el mes (01 ó 07) y la cadena `YYYY` es el año, (desde 2013 hasta la actualidad))
- Tipo: Texto de ancho fijo comprimido (.zip)
- Datos procesados: [/data/codigos_postales_municipiosid_entidades_historical.csv](data/codigos_postales_municipiosid_entidades_historical.csv)

En este caso no se incluye el nombre del municipio. 

### Formato de los datos


Ejemplo en CSV:

|codigo_postal|municipio_id|codigo_unidad_poblacional|entidad_singular_nombre|nucleo_nombre|year|month|
|-------------|------------|-------------------------|-----------------------|-------------|----|-----|
|29610        |29076       |0001701                  |Ojen                   |Ojen         |2013|01   |
|29610        |29076       |0001799                  |Ojen                   |*diseminado* |2013|01   |
|29620        |29076       |0001799                  |Ojen                   |*diseminado* |2013|01   |
|29610        |29076       |0001701                  |Ojen                   |Ojen         |2013|07   |
|29610        |29076       |0001702                  |Ojen                   |Mairena (La) |2013|07   |
|29610        |29076       |0001799                  |Ojen                   |*diseminado* |2013|07   |
|29612        |29076       |0001702                  |Ojen                   |Mairena (La) |2013|07   |

Siguiendo el ejemplo anterior, se aprecia como en Julio de 2013 aparece una nueva unidad poblacional (La Mairena), a la que se le asigna los códigos postales 29610 y 29612.


## Script

El script se puede encontrar en [/scripts/](/scripts/).


## Merge con ds-organizacion-administrativa

Para aquellos conjuntos de datos que no incluan el nombre del municipio asociado al codigo INE, este se puede obtener 
haciendo un merge con `ds-organizacion-administrativa/ds-oa-municipios` mediante `csvjoin`. 

Un ejemplo:

    $ curl https://raw.githubusercontent.com/codeforspain/ds-organizacion-administrativa/master/data/municipios.csv \
        | csvcut -c 'municipio_id,nombre' \
        | csvjoin --snifflimit 0 -I -c "municipio_id" ../data/codigos_postales_municipiosid_entidades.csv - \
        | csvcut -c "codigo_postal,municipio_id,nombre,codigo_unidad_poblacional,entidad_singular_nombre,nucleo_nombre" \
        > ../data/codigos_postales_municipios_entidades.csv
