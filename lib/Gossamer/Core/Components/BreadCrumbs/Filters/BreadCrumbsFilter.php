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
 * Date: 11/7/2017
 * Time: 3:43 PM
 */

namespace Gossamer\Core\Components\BreadCrumbs\Filters;


use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest; use Gossamer\Horus\Http\HttpResponse;

class BreadCrumbsFilter extends AbstractFilter
{

    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {

        $pieces = explode('/', $request->getRequestParams()->getUri());
        //remove empties
        $pieces = array_filter($pieces);

        $breadcrumbs = array();
        $href = '';
        $index = 0;
        $padded = false;
        foreach($pieces as $chunk) {
            $href .= '/' . $chunk;
            if($this->isIgnored($chunk)){
                continue;
            }

            if(strlen($this->filterConfig->get('padFirstCrumb')) > 0 && !$padded) {
                $href .= '/' . $this->filterConfig->get('padFirstCrumb');
                $padded = true;
            }
            $breadcrumbs[] = array(
                'link' => '<a href="' . $href . '">' . str_replace('-', ' ', $chunk) . '</a>',
                'class' => (++$index == count($pieces))? 'active' : ''
            );
        }

        if(!is_null($this->filterConfig->get('breadcrumb'))) {
            $breadcrumbs[] = array(
                'link' => '<a href="#">' . str_replace('-', ' ', $this->filterConfig->get('breadcrumb')) . '</a>',
                'class' => (++$index == count($pieces))? 'active' : ''
            );
        }
        $response->setAttribute('BREADCRUMBS', $breadcrumbs);

        parent::execute($request, $response, $chain); // TODO: Change the autogenerated stub
    }

    private function isIgnored($chunk) {
        $ignored = array(
            'en_US',
            'search-results',
            'recently-viewed'
        );

        return in_array($chunk, $ignored);
    }
}