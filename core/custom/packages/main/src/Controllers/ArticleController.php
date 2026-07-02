<?php namespace EvolutionCMS\Main\Controllers;

use EvolutionCMS\Models\SiteContent;

class ArticleController extends BaseController
{
    public function noCacheRender()
    {
        parent::noCacheRender();

        $id = (int) evo()->documentIdentifier;
        if ($id <= 0 || headers_sent()) {
            return;
        }

        // Front-end requests here don't carry a persisted session (no
        // StartSession middleware on the parser fallback route), so dedupe
        // repeat views with a plain cookie instead of $_SESSION.
        $cookieName = 'evo_viewed_' . $id;
        if (!isset($_COOKIE[$cookieName])) {
            SiteContent::where('id', $id)->increment('hits');
            setcookie($cookieName, '1', time() + 86400, EVO_BASE_URL);
        }
    }
}
