<?php namespace EvolutionCMS\Main\Controllers;

use EvolutionCMS\Main\Helper;
use Illuminate\Support\Collection;

class PageController extends BaseController
{
    public function render()
    {
        parent::render();

        // Only the homepage shows the "popular articles" widget — "page" is
        // also used by About/Categories/Design, which don't need it.
        $this->data['popularArticles'] = evo()->documentIdentifier == evo()->getConfig('site_start')
            ? Helper::popularArticles(3)
            : new Collection();
    }
}
