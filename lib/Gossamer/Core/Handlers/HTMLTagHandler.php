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

use libraries\utils\YAMLKeyParser;

/**
 * HTMLTagHandler - draws html items into the template, used primarily for SEO.
 * 
 * this includes <title>, <description>, as well as new tags such
 * as <og:title> used for search engine results. This reads the 
 * config/html.yml file for 2 parts: 'container' and 'htmltags'.
 * 
 * Container is required so the tag handler can find values in the
 * data array passed into it inside that array key.
 * 
 * Htmltags is required so the handler can see what values from within
 * the container are to be used and where to be inserted into the template.
 * The template will need to contain markers for find|replacing values in.
 * 
 * An example of this is |title| inside the html: <title>|title|</title>
 * 
 * the config would look like this:
 * title: @title
 * or
 * title: 'this is my title value hard coded'
 * 
 * if it is preceeded with an '@' sign it will look for a key called title in 
 * the data array, contained within a sub array named by the 'container' value
 * and insert that into the page.
 * if it is just a hard coded value, that will be placed into the template
 * without pulling any values from the data passed in.
 * 
 * the yml file can contain any keys required as long as the matching markers
 * are placed in the html template.
 * 
 * if no config is placed in the yml file for that yml key a 'default' key
 * MUST be specified in the yml with the appropriate values and the tag handler 
 * will simply use the default values for any page that has no custom key specified.
 * 
 * @author Dave Meikle
 */
class HTMLTagHandler extends BaseHandler {

    private $template = null;
    private $URIKeys = null;

    /**
     * 
     * @param array $params
     * 
     * @return string
     */
    public function handleRequest($params = array()) {

        $config = $this->loadConfig();

        if (!is_array($config) || !array_key_exists('htmltags', $config)) {
            return $this->template;
        }

        $this->getTagValues($config['htmltags'], $params);

        return $this->template;
    }

    /**
     * loads the configuration if exists
     * 
     * @return null|array
     */
    private function loadConfig() {

        $loader = new YAMLKeyParser($this->logger);
        $loader->setFilePath(__SITE_PATH . DIRECTORY_SEPARATOR . __NAMESPACE . DIRECTORY_SEPARATOR . ((strpos(__NAMESPACE, 'framework') !== false) ? 'core' . DIRECTORY_SEPARATOR : '') .
                __COMPONENT_FOLDER . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'html.yml');
        $config = $loader->loadConfig();

        if (!is_array($config)) {
            //there is no html.yml file existing so don't go any further
            return null;
        }

        if (array_key_exists(__YML_KEY, $config)) {
            return $config[__YML_KEY];
        }

        return $config['default'];
    }

    /**
     * loads tag values from yml which can be hard coded or dynamic
     * 
     * @param array $htmlTags
     * @param array $params
     */
    private function getTagValues(array $htmlTags, array $params) {
        $tags = $htmlTags['tags'];

        if (array_key_exists('container', $htmlTags) && array_key_exists($htmlTags['container'], $params)) {
            $item = $params[$htmlTags['container']];
            reset($item);
            $firstKey = key($item);
            if (is_numeric($firstKey)) {
                //let's assume we've got a single item to deal with
                $item = current($item);
            }
        } else {
            $item = $params;
        }

        //go through list of tags, if it has '@' use a dynamic value from
        //the passed in params. if not, then use a static value from the yml file
        foreach ($tags as $key => $tag) {
            if (substr($tag, 0, 1) == '@') {

                //check to ensure it's in the passed in params
                $value = $this->findKey($item, substr($tag, 1));

                if ($value !== false) {
                    $this->template = str_replace("|$key|", $value, $this->template);
                } else {
                    //just put the hardcoded value in from the config file
                    $this->template = str_replace("|$key|", $tag, $this->template);
                }
            } else {
                //just put the hardcoded value in from the config file
                $this->template = str_replace("|$key|", $tag, $this->template);
            }
        }
    }

    /**
     * 
     * @param array $array
     * @param string $key
     * 
     * @return boolean
     */
    private function findKey(array $array, $key) {

        //check for a locale based string first
        if (array_key_exists('locales', $array) && array_key_exists('locale', $array)) {
            if (array_key_exists($key, $array['locales'][$array['locale']])) {

                return $array['locales'][$this->defaultLocale['locale']][$key];
            }
        }

        if (array_key_exists($key, $array)) {

            return $array[$key];
        }

        return false;
    }

    /**
     * accessor
     * 
     * @param string $template
     */
    public function setTemplate($template) {
        $this->template = $template;
    }

}
