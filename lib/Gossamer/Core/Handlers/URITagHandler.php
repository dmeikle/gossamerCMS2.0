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
use libraries\utils\YAMLKeyParser;

/**
 * looks for uri tags using a yml key and places the real uri in there
 *
 * @author Dave Meikle
 */
class URITagHandler extends BaseHandler {

    protected $template = null;
    private $URIKeys = null;

    /**
     *
     * @param array $params
     *
     * @return string
     */
    public function handleRequest($params = array()) {
        // <gcms:uri='cart_admin_categories_list'/>
        $keys = $this->getURIKeys();

        $tags = $this->getURITagKeys();

        $links = $this->buildLinks($tags);

        $this->insertLinks($links);

        return $this->template;
    }

    protected function buildLinks($tags) {

        $links = $tags[0];
        $params = $tags[1];
        $router = new \core\system\Router($this->logger, $this->httpRequest);
        $retval = array();
        for ($i = 0; $i < count($links); $i++) {
            $linkParams = $this->formatParams($params[$i]);
            $key = "<gcms:uri='" . $links[$i] . ((count($linkParams) > 0) ? "'" . $params[$i] : "'") . '/>';

            $retval[$key] = $router->getQualifiedUrl($links[$i], $linkParams);
        }

        return $retval;
    }

    protected function formatParams($item) {
        $pieces = explode("'", $item);
        if (count($pieces) == 1) {
            return array();
        }

        $chunks = explode('/', $pieces[1]);

        return $chunks;
    }

    /**
     *
     * @param array $keys
     */
    function insertLinks($links) {
        if (is_null($links)) {
            return;
        }
        $keys = array_keys($links);
        foreach ($links as $key => $value) {
            $this->template = str_replace($key, $value, $this->template);
        }
    }

    /**
     *
     * @return array
     */
    protected function getURITagKeys() {
        //<gmcs:uri=cart_admin_categories_list/>
        $pattern = "/<gcms:uri='(.*?)'(.*?)\/>/";
        preg_match_all($pattern, $this->template, $matches);

        array_shift($matches);

        return $matches;
    }

    /**
     *
     * @return array
     */
    private function getURIKeys() {
        if (is_null($this->URIKeys)) {
            $parser = new YAMLKeyParser($this->logger);
            $this->URIKeys = $parser->getKeys();
            unset($parser);
        }

        return $this->URIKeys;
    }

    /**
     * accessor
     *
     * @param string $template
     */
    public function setTemplate(&$template) {
        $this->template = $template;
    }

}
