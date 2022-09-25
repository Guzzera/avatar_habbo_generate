bbbb<?php

class Avatar {


    /*
    *
    * Compara a última versão de Avatar
    *
    * @type     string
    *
    */

    protected $version = "1.0.3";


    /*
    *
    * Seta o diretório que contém os arquivos json
    *
    * @type     string
    *
    */

    protected $output = __DIR__ . '/json';


    /*
    *
    * Constrói a classe e chama o método 'convertToJSON' 
    *
    */

    public function __construct() {

        $this->convertToJSON ('http://habboo-a.akamaihd.net/gordon/PRODUCTION-201606242205-761645438/figuremap.xml');
    }


    /*
     * Carrega um arquivo XML e depois monta
     * em um formato amigável ao json que
     * ser utilizado pelo plugin jQuery.
     *
     */

     public function convertToJSON($xmlFile) {

        $xmlFileContents = file_get_contents($xmlFile);
        $xmlFileContents = simplexml_load_string($xmlFileContents);


    /*
     *
     * This creates the output file: palettes.json
     * 
     */ 
    
     $palettes = array();

     foreach($xmlFileContents->xpath('colors/palette') as $palette) {

        $id = (int) $palette->attributes()->id;

        $palletes[$id] = array();

        foreach($palette->xpath('color') as $color) {

            $colorID = (int) $color->attributes()->id;

            $palletes[$id] [$colorID] = array(
                'index'       => (int) 
                $color->attributes()->index,
                'cluub'       => (int)
                $color->attributes()->club,
                'selectable'  => (int)
                $color->attributes()->selectable,
                'hex'         => (string) $color,
            );
        }
    }

    $toJSON = json_encode($palletes);

    if($this->writeJson("{this->output}/palettes.json", $toJSON))
        echo "Successfully wrote palettes.json </br>";

    else
        echo "Could not write to file paletters.json </br>";

    
    /*
    *
    * Cria a saida do arquivo: settypes.json
    */

    $settypes = array();

    foreach($xmlFileContents->xpath('sets/settype') as $key => $settype) {

        $settypes[$key] = array(
            'paletteid' => (int)
            $settype->attributes()->paletteid,
            'type'      => (string)
            $settype->attributes()->type,
            'sets'      => array(),
        );

        foreach($settype->xpath('set') as $set) {

            $id = (int) $set->attributes()->id;

            $settypes[ $key ]['sets'][ $id ] = array(
                'gender'        => (string)
                $set->attributes()->gender,
                'club'          => (int)
                $set->attributes()->club,
                'colorable'     => (int)
                $set->attributes()->colorable,
                'selectable'    => (int)
                $set->attributes()->selectable,
                'preselectable' => (int)
                $set->attributes()->preselectable,
            );
        }
    }

    $toJSON = json_encode($settypes);
    if($this->writeJson("{this->output}/settypes.json", $toJSON))
        echo "Successfully wrote settypes.json </br>";

    else
        echo "Could not write to file settypes.json </br>";

    }


    /*
    * Pega um arquivo de saída e escreve pra ele.
    * Cria o diretório com base na variável 
    * $output caso não exista
    *
    * @param    $outputFile string
    * @param    $json       string
    *
    * @return   boolean
    */

    private function writeJson( $outputFile, $json ) {

        if( is_writable( $this->output)) {

            if(!is_dir($this->output)) {

                mkdir( $this->output);
            }

            $f = fopen( $outputFile, "w");

            fwrite( $f, $json );
            fclose( $f );

            return true;
        }

        return false;
    }
}