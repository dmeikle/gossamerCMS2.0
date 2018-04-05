<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace core\handlers;

use core\handlers\BaseHandler;

/**
 * Each component can have their own CSS included. This allows the developer
 * to create a packaged component with all the css it needs. Once the component
 * is accessed the first time, this class will copy the css file into the
 * public /css/components/<name-of-component> folder. From there, it always
 * refers to the public file. It will only rewrite (automatically)
 * if the CSS is updated.
 * files are copied from the component's includes/js folder
 * To reference them, place them in the .php file (in the view) inside of:
 *
 * <!--- css start --->
 * @components/component-name/includes/css/name-of-file.css
 * @components/component-name/includes/css/name-of-another-file.css
 * <!--- css end --->
 *
 * @author Dave Meikle
 */
class ImportCSSHandler extends BaseHandler {

    /**
     *
     * @param array $params
     *
     * @return array
     */
    public function handleRequest($params = array()) {
        $retval = array();
        //check to see if there are any escaped rows then import them
        foreach ($params as $row) {
            $tmp = trim($row);

            if (substr($tmp, 0, 11) == '@components') {

                $filepath = str_replace('@components', '/components', $tmp);

                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale($filepath, 'css')) {
                    $this->copyFile($filepath, 'css');
                }

                $retval[] = '/css' . str_replace('includes/css/', '', $filepath);
            } elseif (substr($tmp, 0, 16) == '@core/components') {
                $filepath = str_replace('@core/components', '/framework/core/components', $tmp);

                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale($filepath, 'css')) {
                    $this->copyFile($filepath, 'css');
                }

                $retval[] = '/css' . str_replace('includes/css/', '', $filepath);
            } elseif (substr($tmp, 0, 9) == '@/assets/') {

                //nothing to copy - we are simply going to the root of the
                //website components folder
                $retval[] = str_replace('@/assets', '/assets', $tmp);
            } elseif (substr($tmp, 0, 12) == '@extensions/') {

                $filepath = str_replace('@extensions/', '/extensions/', $tmp);

                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale($filepath, 'css')) {
                    $this->copyFile($filepath, 'css');
                }

                $retval[] = str_replace('@extensions', '/css/extensions', $tmp);
            } elseif (strlen($tmp > 5)) {//abitrary length just to show we hold something greater than whitespace
                $retval[] = $tmp;
            }
        }

        return array_filter($retval);
    }

}
