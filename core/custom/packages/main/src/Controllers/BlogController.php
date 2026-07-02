<?php namespace EvolutionCMS\Main\Controllers;

use Seiger\sLang\Models\sLangContent;

class BlogController extends BaseController
{
    protected int $perPage = 2;

    public function render()
    {
        parent::render();

        $this->data['articles'] = sLangContent::lang(evo()->getLocale())
            ->active()
            ->addSelect('site_content.createdon as createdon_orig')
            ->where('site_content.parent', evo()->documentIdentifier)
            ->orderBy('site_content.menuindex')
            ->paginate($this->perPage);
    }
}
