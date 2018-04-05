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
 * Date: 9/15/2017
 * Time: 2:58 PM
 */

namespace Gossamer\Core\Views;


use Gossamer\Core\MVC\AbstractView;

class HtmlEmailTemplateView extends AbstractView
{

    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;

    protected $template;

    protected $sections = null;
    private $isMobile = false;
    private $jsIncludeFiles = array();
    private $cssIncludeFiles = array();
    private $headFiles = array();
    private $data;

    public function render($data = array()) {

        //get any preloaded items that are in the Response object
        $data = array_merge(is_null($data) ? array() : $data, $this->httpResponse->getAttributes());
       
        $this->loadTemplate();

        // The second parameter of json_decode forces parsing into an associative array
        //we extract to make data visible across the template
        extract(json_decode(json_encode($data), true));


        //include all sections before we render anything
        $this->renderSections();


        // $this->renderHTMLTags();
        $this->placeHeadJSFiles();
//        $this->placeJSFiles();
        $this->placeCSSFiles();
//        $this->renderURITags($template);

        file_put_contents(__DEBUG_OUTPUT_PATH, $this->template, FILE_APPEND);


        ob_start();
        (eval("?>" . $this->template));
        $result = ob_get_clean();

        return array('data' => $result);
    }

    private function loadTemplate() {
        $viewConfig = $this->loadViewConfig();
        $siteConfig = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'config.yml');
        $this->template = $viewConfig['template'];
        $this->sections = $viewConfig['sections'];
        $theme = $siteConfig['theme'][$viewConfig['themetype']];

        $templatePath = $this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'themes' .
            DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $viewConfig['template'];

        $this->template = file_get_contents($templatePath);

    }

    private function loadViewConfig() {
        $nodeConfig = $this->httpRequest->getNodeConfig();

        $viewKey = $nodeConfig[$nodeConfig['ymlKey']]['defaults']['viewKey'];
        $viewConfigPath = $this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'views.yml';

        $viewConfig = loadConfig($viewConfigPath, $viewKey);

        return $viewConfig;
    }

    protected function isValueSet($option, $values) {

        if(!is_array($values)) {
            return ($values) == $option;
        }
        if(is_array($values) && in_array($option,$values)){
            return true;
        }

        return false;
    }

    /**
     * render the HTML tags
     */
    protected function renderHTMLTags() {

        $htmlHandler = new HTMLTagHandler($this->logger);
        $htmlHandler->setDefaultLocale($this->getDefaultLocale());
        $htmlHandler->setTemplate($this->template);
        $this->template = $htmlHandler->handleRequest($this->data);
    }


    /**
     * finds all sections within a template and places the appropriate PHP
     * file within that area of the template before rendering all PHP tags
     *
     * @return void
     */
    protected function renderSections() {

        if (is_null($this->sections)) {
            return;
        }

        foreach ($this->sections as $sectionName => $section) {
            if (!is_array($section)) {
                $sectionNamePlaceHolder = '<!---' . $sectionName . '--->';
                $this->template = str_replace($sectionNamePlaceHolder, $this->loadSectionContent($section), $this->template);
            } else {
                foreach ($section as $subSectionName => $subSection) {
                    $sectionNamePlaceHolder = '<!---' . $subSectionName . '--->';
                    $this->template = str_replace($sectionNamePlaceHolder, $this->loadSectionContent($subSection), $this->template);
                }
            }
        }
    }


    /**
     * loads the PHP 'section' and renders and includes for JS/CSS
     *
     * @param string $section
     *
     * @return string
     */
    private function loadSectionContent($section) {

        $sectionContent = file_get_contents($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $section);

        return $sectionContent;

        //not implemented yet
//        $contentWithHeadJs = $this->renderHeadJs($sectionContent);
//        $contentWithJs = $this->renderJs($contentWithHeadJs);
//
//        $contentWithCss = $this->renderCss($contentWithJs);
//
//        if (is_array($contentWithCss)) {
//            return implode('', $contentWithCss);
//        }
//
//        return $contentWithCss;
    }

    /**
     * find any JS files to create <script /> tags for
     */
    protected function placeHeadJSFiles() {
        $jsIncludeString = '';
        //remove any duplicates from files calling same includes
        $list = array_unique($this->headFiles);

        foreach ($list as $file) {
            $jsIncludeString .= "<script language=\"javascript\" src=\"$file\"></script>\r\n";
        }

        $this->template = str_replace('<!---head--->', $jsIncludeString, $this->template);
    }

    /**
     * find any JS files to create <script /> tags for
     */
    protected function placeJSFiles() {
        $jsIncludeString = '';
        //remove any duplicates from files calling same includes
        $list = array_unique($this->jsIncludeFiles);

        foreach ($list as $file) {
            $jsIncludeString .= "<script language=\"javascript\" src=\"$file\"></script>\r\n";
        }
//        $newlist = array();
//        foreach ($list as $item) {
//            $newlist[] = substr($item, 3);
//        }
//        $file = implode(',', $newlist);
//        $jsIncludeString .= "<script language=\"javascript\" src=\"/compression/js?files=$file\"></script>\r\n";
        $this->template = str_replace('<!---javascript--->', $jsIncludeString, $this->template);
    }

    /**
     * find any CSS files to create <script /> tags for
     */
    protected function placeCSSFiles() {
        $cssIncludeString = '';
        //remove any duplicates from files calling same includes
        $list = array_unique($this->cssIncludeFiles);

        foreach ($list as $file) {
            $cssIncludeString .= "<link href=\"$file\" rel=\"stylesheet\">\r\n";
        }

        $this->template = str_replace('<!---css--->', $cssIncludeString, $this->template);
    }

}