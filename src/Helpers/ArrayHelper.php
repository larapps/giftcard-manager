<?php

namespace Larapps\GiftCertificateManager\Helpers;

class ArrayHelper {

    public static function chunkFile( $path, callable $generator, int $chunkSize){

        $path = storage_path("app/private/".$path);

        $file = fopen($path, 'r');
        $data = [];

        $headerRow = [];

        for($index=1; ($row = fgetcsv($file, null, ',')) != false; $index++ ){

            /** Remove unwanted formaatting in csv file */
            foreach($row as $formatIndex => $formatRow){
                $row[$formatIndex] = trim($row[$formatIndex], "\xEF\xBB\xBF");
            }

            if($index === 1){
                $headerRow = $row;
                continue;
            }


            if(!empty($row[0])){
                $data[] = $generator($row, $headerRow);
            }


            if($index % $chunkSize === 0){
                yield $data;

                $data = [];
            }

        }

        if(!empty($data)){
            yield $data;
        }

        fclose($file);
    }
}