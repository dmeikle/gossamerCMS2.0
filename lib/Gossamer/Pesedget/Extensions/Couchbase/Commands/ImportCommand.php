<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/16/2017
 * Time: 4:22 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Commands;


class ImportCommand extends SaveCommand
{

    protected function getFilepath() {
        $siteParams = $this->httpRequest->getSiteParams();

        return $siteParams->getSitePath();
    }

    public function execute($params = array(), $post = array()) {
        $path = $this->getFilepath() . DIRECTORY_SEPARATOR . $params['path'];
        $type = $params['type'];
        $keys = null;
        $arrayColumns = array_key_exists('arrayColumns', $params) ? array($params['arrayColumns']) : null;

        $file_handle = fopen($path, 'r');
        $count=0;
        while (!feof($file_handle)) {
            $this->entity = new $type();
            $line_of_text = fgetcsv($file_handle, 1024);
            //get the keys to assign
            if(is_null($keys)) {
                $keys = $line_of_text;
                $line_of_text = null;
                continue;
            }
            if($line_of_text === false) {
                break;
            }

            parent::execute(array(), $this->setKeyValuePairs($keys, $line_of_text, $arrayColumns));
      
        }

        fclose($file_handle);
        echo "complete";
    }

    private function setKeyValuePairs(array $keys, array $row, $arrayColumns = null) {
        $retval = array();


        foreach($keys as $key) {
            $columnValue = array_shift($row);
            if(!is_null($arrayColumns) && in_array($key, $arrayColumns)) {
                $retval[$key] = array($columnValue);
            }else{
                $retval[$key] = $columnValue;
            }

        }
        

        return $retval;
    }
}