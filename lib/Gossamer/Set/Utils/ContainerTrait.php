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
 * Time: 10:02 PM
 */

namespace Gossamer\Set\Utils;

use Gossamer\Set\Utils\Container;

trait ContainerTrait
{
    protected $container;

    /**
     * @return mixed
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return ContainerTrait
     */
    public function setContainer(Container &$container) {
        $this->container = $container;
        return $this;
    }


}