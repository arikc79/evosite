<?php namespace EvolutionCMS\Main\Controllers;

use EvolutionCMS\Main\Helper;
use Illuminate\Support\Collection;

class PageController extends BaseController
{
    public function noCacheRender()
    {
        parent::noCacheRender();

        // Popular-articles counts must be read outside the cached render()
        // block (BaseController caches render() under one shared key), or
        // the widget freezes at whatever hits were current on first cache fill.
        // Only the homepage shows the widget — "page" is also used by
        // About/Categories/Design, which don't need it.
        $this->data['popularArticles'] = evo()->documentIdentifier == evo()->getConfig('site_start')
            ? Helper::popularArticles(3)
            : new Collection();
    }
}
