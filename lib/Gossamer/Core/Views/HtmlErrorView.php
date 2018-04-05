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
 * Date: 3/5/2017
 * Time: 9:15 AM
 */

namespace Gossamer\Core\Views;


use Gossamer\Core\System\SiteParams;

class HtmlErrorView
{

    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function render(array $params = null) {
        readfile($this->path);
    }
}