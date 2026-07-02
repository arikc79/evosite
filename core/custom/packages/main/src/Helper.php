<?php namespace EvolutionCMS\Main;

use EvolutionCMS\Models\SiteTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Seiger\sCommerce\Facades\sCommerce;
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
    | Breadcrumbs generator
    |--------------------------------------------------------------------------
    */
    public static function breadcrumbs()
    {
        $identifier = evo()->documentObject['type'] == 'product' ? evo()->documentObject['category'] : evo()->documentIdentifier;
        $parentIds = array_reverse(evo()->getParentIds($identifier));

        if (evo()->getConfig('site_root') && in_array(evo()->getConfig('site_root'), $parentIds)) {
            $parentIds = array_diff($parentIds, [evo()->getConfig('site_root')]);
        }

        array_unshift($parentIds, evo()->getConfig('site_start'));

        if (evo()->documentObject['type'] == 'product') {
            array_push($parentIds, evo()->documentObject['category']);
        }

        $breadcrumbs = SiteContent::select('*', 'site_content.id as key')->whereIn('site_content.id', $parentIds);

        if (evo()->getConfig('site_start', false)) {
            $breadcrumbs->leftJoin('s_lang_content', 's_lang_content.resource', '=', 'site_content.id');
            $breadcrumbs->where('s_lang_content.lang', evo()->getLocale());
        }

        $breadcrumbs->orderByRaw('FIELD(`key`, ' . implode(', ', $parentIds) . ')');
        $breadcrumbs->where('site_content.hidemenu', 0);

        return $breadcrumbs->active()->get();
    }
    
    /*
    |--------------------------------------------------------------------------
    | MultiFields Normalizer
    |--------------------------------------------------------------------------
    */
    public static function multiFields($data)
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
    
    public function backcallExample()
    {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            die(json_encode(['errors' => $validator->errors()->messages()]));
        }

        sCommerce::notifyEmail(
            explode(',', sCommerce::config('notifications.email_addresses', '')),
            "notifications/email/adminCallback.blade.php",
            $validator->validated()
        );

        die(json_encode(['success' => true]));
    }
}
