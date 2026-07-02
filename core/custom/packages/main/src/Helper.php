<?php namespace EvolutionCMS\Main;

use EvolutionCMS\Models\SiteTemplate;
use Illuminate\Support\Facades\DB;
use Seiger\sGallery\Facades\sGallery;
use Seiger\sLang\Models\sLangContent;

class Helper
{
    /*
    |--------------------------------------------------------------------------
    | First gallery image for a document, as an <img> tag (or '' if none)
    |--------------------------------------------------------------------------
    */
    public static function articleThumb(int $documentId): string
    {
        $item = sGallery::collections()->documentId($documentId)->get()
            ->first(fn ($item) => sGallery::hasImage($item->type));

        if (!$item) {
            return '';
        }

        return '<img src="' . htmlspecialchars($item->src) . '" alt="" style="max-width:400px;height:auto;">';
    }

    /*
    |--------------------------------------------------------------------------
    | Human-readable date ("30 червня 2026" / "30 June 2026") — a manual
    | month-name lookup rather than PHP's locale-dependent strftime/IntlDateFormatter,
    | so it doesn't depend on the server having the right locale installed.
    |--------------------------------------------------------------------------
    */
    public static function formatDate(int $timestamp): string
    {
        $months = [
            'uk' => ['січня', 'лютого', 'березня', 'квітня', 'травня', 'червня', 'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'],
            'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        ];

        $locale = evo()->getLocale();
        $monthNames = $months[$locale] ?? $months['en'];
        $monthName = $monthNames[((int) date('n', $timestamp)) - 1];

        return date('j', $timestamp) . ' ' . $monthName . ' ' . date('Y', $timestamp);
    }

    /*
    |--------------------------------------------------------------------------
    | Most-viewed published articles (falls back to newest when nothing has
    | been viewed yet, so the widget isn't empty on a fresh install)
    |--------------------------------------------------------------------------
    */
    public static function popularArticles(int $limit = 3)
    {
        $articleTemplateId = SiteTemplate::where('templatealias', 'article')->value('id');

        $query = sLangContent::lang(evo()->getLocale())
            ->active()
            ->addSelect('site_content.hits as hits', 'site_content.createdon as createdon_orig');

        if ($articleTemplateId) {
            $query->where('site_content.template', $articleTemplateId);
        }

        $hasViews = (clone $query)->where('site_content.hits', '>', 0)->exists();

        return $hasViews
            ? $query->orderByDesc('site_content.hits')->limit($limit)->get()
            : $query->orderByDesc('site_content.createdon')->limit($limit)->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs generator — ancestor chain (Home first), NOT including the
    | current document itself; the calling view renders the current page's
    | own title as the final, non-linked crumb.
    |--------------------------------------------------------------------------
    */
    public static function breadcrumbs()
    {
        // evo()->getParentIds() returns [childId => parentId, ...] walking up
        // from the current document to the root, so its values (not keys) are
        // the ancestor chain, nearest-parent-first — reverse for Home-first order.
        $parentIds = array_reverse(array_values(evo()->getParentIds(evo()->documentIdentifier)));
        array_unshift($parentIds, evo()->getConfig('site_start'));
        $parentIds = array_values(array_unique($parentIds));

        if (empty($parentIds)) {
            return sLangContent::whereRaw('1 = 0')->get();
        }

        // orderByRaw() doesn't get the table prefix applied automatically,
        // unlike where()/whereIn(), so it has to be added by hand here.
        $prefixedTable = DB::getTablePrefix() . 'site_content';

        return sLangContent::lang(evo()->getLocale())
            ->active()
            ->where('site_content.hidemenu', 0)
            ->whereIn('site_content.id', $parentIds)
            ->orderByRaw("FIELD({$prefixedTable}.id, " . implode(',', $parentIds) . ')')
            ->get();
    }
    
    /*
    |--------------------------------------------------------------------------
    | MultiFields Normalizer
    |--------------------------------------------------------------------------
    */
    public static function multiFields(array $data)
    {
        $newdata = [];
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                if (isset($item['items']) && is_array($item['items'])) {
                    if (stripos($item['name'],'_group')){
                        foreach ($item['items'] as $k => $v) {
                            $newdata[$item['name']][$k] = self::multiFields($v['items']);
                        }
                    } else {
                        $newdata[$key]['name'] = $item['name'];
                        $newdata[$key]['items'] = self::multiFields($item['items']);
                    }
                } else{
                    $newdata[$item['name']] = $item['value'] ?? '';
                }
            }
        }
        return $newdata;
    }
}
