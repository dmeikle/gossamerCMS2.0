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
 * Each component can have their own images included. This allows the developer
 * to create a packaged component with all the images it needs. Once the component
 * is accessed the first time, this class will copy the image file into the
 * public /images/components/<name-of-component> folder. From there, it always
 * refers to the public file. It will only rewrite (automatically)
 * if the image is updated.
 * files are copied from the component's includes/images folder
 * To reference them, place them in the view's .php file (in the view) inside of:
 *
 * <img src="@components/component-name/includes/images/name-of-file.png" />
 * <img src="@components/component-name/includes/images/name-of-another-file.jpg" />
 *
 * @author Dave Meikle
 */
class ImportImageHandler extends BaseHandler {
    /**
     *
     * @param array $params
     *
     * @return array
     */
//    public function handleRequestByFileList($params = array()) {
//        $retval = array();
//        //check to see if there are any escaped rows then import them
//        foreach ($params as $row) {
//            $tmp = trim($row);
//
//            if (substr($tmp, 0, 11) == '@components') {
//
//                $filepath = str_replace('@components', '', $tmp);
//
//                //we need to import this if it doesn't exist or if the existing is stale
//                if ($this->checkFileIsStale($filepath, 'images')) {
//                    $this->copyFile($filepath, 'images');
//                }
//
//                $retval[] = '/images/components' . str_replace('includes/images/', '', $filepath);
//            } elseif (strlen($tmp > 5)) {//abitrary length just to show we hold something greater than whitespace
//                $retval[] = $tmp;
//            }
//        }
//
//        return array_filter($retval);
//    }
//

    /**
     * copies the images for this component to the public web folder
     * @param type $params
     */
    public function handleRequest($params = array()) {

        $directory = __SITE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . __COMPONENT_FOLDER .
                DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

        $files = @scandir($directory);
        if (!is_array($files)) {
            return;
        }
        foreach ($files as $file) {

            if ($file != '.' && $file != '..') {
                //we need to import this if it doesn't exist or if the existing is stale
                if ($this->checkFileIsStale(DIRECTORY_SEPARATOR . $file, 'images')) {
                    $this->copyFile(DIRECTORY_SEPARATOR . __COMPONENT_FOLDER . DIRECTORY_SEPARATOR . 'includes' .
                            DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $file, 'images');
                }
            }
        }
    }

}
