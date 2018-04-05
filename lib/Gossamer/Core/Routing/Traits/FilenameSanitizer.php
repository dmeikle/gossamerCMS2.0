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
 * Date: 3/8/2017
 * Time: 10:27 PM
 */

namespace Gossamer\Core\Routing\Traits;


trait FilenameSanitizer
{

    /**
     * Sanitizes a filename replacing whitespace with dashes
     *
     * Removes special characters that are illegal in filenames on certain
     * operating systems and special characters requiring special escaping
     * to manipulate at the command line. Replaces spaces and consecutive
     * dashes with a single dash. Trim period, dash and underscore from beginning
     * and end of filename.
     *
     * @param string $string The filename to be sanitized
     * @param bool $is_filename
     * @return string The sanitized filename
     */
    protected function sanitizeFilename($filename = '', $is_filename = FALSE) {

        $pieces = array_filter(explode('/', $filename));


        $str = strip_tags($filename);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '_', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '_', $str);

        rtrim($str, '/');
        return implode(DIRECTORY_SEPARATOR, $pieces) . DIRECTORY_SEPARATOR . $str . '_' . $this->httpRequest->getRequestParams()->getMethod();
    }

}