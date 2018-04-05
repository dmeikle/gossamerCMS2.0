<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace Gossamer\Core\Handlers;

/**
 * Each component can have their own JS included. This allows the developer
 * to create a packaged component with all the css it needs. Once the component
 * is accessed the first time, this class will copy the css file into the
 * public /js/components/<name-of-component> folder. From there, it always
 * refers to the public file. It will only rewrite (automatically)
 * if the js is updated.
 * files are copied from the component's includes/js folder
 * To reference them, place them in the .php file (in the view) inside of:
 *
 * <!--- javascript start --->
 * @components/component-name/includes/js/name-of-file.js
 * @components/component-name/includes/js/name-of-another-file.js
 *
 * //by placing a preceding '/' before the path, this tells the filter to ignore
 * //the import from the component folder and just grab it from the root of the website
 * @/components/component-name/name-of-file.js
 *
 * <!--- javascript end --->
 *
 * @author Dave Meikle
 */
class ImportJSHandler extends BaseHandler {

    /**
     *
     * @param array $params
     *
     * @return type
     */
    public function handleRequest($params = array()) {

        $retval = array();
        //check to see if there are any escaped rows then import them
        foreach ($params as $row) {
            $tmp = trim($row);

            if (substr($tmp, 0, 11) == '@components') {

                $filepath = str_replace('@components', '/components', $tmp);

                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale($filepath, 'js')) {
                    $this->copyFile($filepath, 'js');
                }

                $retval[] = '/js' . str_replace('includes/js/', '', $filepath);
            } elseif (substr($tmp, 0, 16) == '@core/components') {

                $filepath = str_replace('@core/components', '/framework/core/components', $tmp);

                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale($filepath, 'js')) {
                    $this->copyFile($filepath, 'js');
                }

                $retval[] = '/js' . str_replace('includes/js/', '', $filepath);
            } elseif (substr($tmp, 0, 13) == '@/components/') {

                //nothing to copy - we are simply going to the root of the
                //website components folder
                $retval[] = str_replace('@/components', '/components', $tmp);
            } elseif (substr($tmp, 0, 9) == '@/assets/') {

                //nothing to copy - we are simply going to the root of the
                //website components folder
                $retval[] = str_replace('@/assets', '/assets', $tmp);
            } elseif (substr($tmp, 0, 12) == '@extensions/') {

                $filepath = str_replace('@extensions/', '/extensions/', $tmp);

                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale($filepath, 'js')) {
                    $this->copyFile($filepath, 'js');
                }

                $retval[] = str_replace('@extensions', '/js/extensions', $tmp);
            } elseif (strlen($tmp) > 5) {//abitrary length just to show we hold something greater than whitespace
                $retval[] = $tmp;
            }
        }

        return array_filter($retval);
    }

}
