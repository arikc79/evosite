<?php namespace EvolutionCMS\Controllers\Resources;

use EvolutionCMS\Models;
use EvolutionCMS\Controllers\AbstractResources;
use EvolutionCMS\Interfaces\ManagerTheme\TabControllerInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent;

class Templates extends AbstractResources implements TabControllerInterface
{
    protected $view = 'page.resources.templates';

    /**
     * {@inheritdoc}
     */
    public function getTabName($withIndex = true): string
    {
        if ($withIndex) {
            return 'tabPlugins-' . $this->getIndex();
        }

        return 'tabTemplates';
    }

    /**
     * {@inheritdoc}
     */
    public function canView(): bool
    {
        return $this->managerTheme->getCore()->hasAnyPermissions([
            'new_template',
            'edit_template'
        ]);
    }

    protected function getBaseParams()
    {
        return array_merge(
            parent::getParameters(),
            [
                'tabPageName'      => $this->getTabName(false),
                'tabIndexPageName' => $this->getTabName()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(array $params = []) : array
    {
        $params = array_merge($this->getBaseParams(), $params);

        if ($this->isNoData()) {
            return $params;
        }

        return array_merge([
            'categories' => $this->parameterCategories(),
            'outCategory' => $this->parameterOutCategory()
        ], $params);
    }

    protected function parameterOutCategory() : Collection
    {
        return Models\SiteTemplate::where('category', '=', 0)
            ->orderBy('templatename', 'ASC')
            ->lockedView()
            ->get();
    }

    protected function parameterCategories() : Collection
    {
        return Models\Category::with('templates')
            ->whereHas('templates', function (Eloquent\Builder $builder) {
                return $builder->lockedView();
            })->orderBy('rank', 'ASC')
            ->get();
    }
}
