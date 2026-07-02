<?php

use EvolutionCMS\Main\Helper;
use EvolutionCMS\Models\SiteContent;
use Illuminate\Support\Facades\DB;
use Seiger\sGallery\Models\sGalleryModel;

/**
 * These tests write to the real `evosite` database (there is no separate
 * test DB configured for this project), so every test runs inside a
 * transaction that's rolled back afterwards — nothing here persists.
 *
 * They need the full app container (DB connection, model bindings, the
 * Main package's service provider) booted, same as a real request — plain
 * PHPUnit/Pest bootstrap alone doesn't set that up.
 */
beforeEach(function () {
    if (!defined('IN_INSTALL_MODE')) {
        define('IN_INSTALL_MODE', false);
    }
    if (!defined('EVO_API_MODE')) {
        define('EVO_API_MODE', true);
    }
    if (!defined('IN_MANAGER_MODE')) {
        define('IN_MANAGER_MODE', false);
    }
    if (!defined('IN_PARSER_MODE')) {
        define('IN_PARSER_MODE', false);
    }

    require_once dirname(__DIR__, 3) . '/core/bootstrap.php';

    \EvolutionCMS\Core::getInstance();

    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

test('attaching a gallery image via firstOrCreate + update does not crash', function () {
    // Reproduces the exact sGalleryController upload flow: firstOrCreate()
    // with no attributes inserts a blank row first, then the fields are
    // filled in via update(). Regression test for the migration that gave
    // s_galleries.parent/.file defaults (2026_07_02_000000_fix_s_galleries_missing_defaults).
    $article = SiteContent::create([
        'pagetitle' => 'Test article for gallery',
        'template' => 3,
        'parent' => 2,
        'published' => 1,
    ]);

    $thisFile = sGalleryModel::whereParent($article->id)
        ->whereBlock('1')
        ->whereItemType('resource')
        ->whereFile('test-image.jpg')
        ->firstOrCreate();
    $thisFile->parent = $article->id;
    $thisFile->block = '1';
    $thisFile->file = 'test-image.jpg';
    $thisFile->type = 'image';
    $thisFile->item_type = 'resource';
    $thisFile->update();

    expect($thisFile->id)->toBeGreaterThan(0)
        ->and($thisFile->parent)->toBe($article->id)
        ->and($thisFile->file)->toBe('test-image.jpg');
});

test('article thumb helper returns empty string when no gallery image exists', function () {
    $article = SiteContent::create([
        'pagetitle' => 'Article without image',
        'template' => 3,
        'parent' => 2,
        'published' => 1,
    ]);

    expect(Helper::articleThumb($article->id))->toBe('');
});

test('article thumb helper returns a capped image tag when a gallery image exists', function () {
    $article = SiteContent::create([
        'pagetitle' => 'Article with image',
        'template' => 3,
        'parent' => 2,
        'published' => 1,
    ]);

    // sGalleryModel::getSrcAttribute() falls back to a placeholder unless
    // the file actually exists on disk, so this has to point at a real file.
    $galleryItem = new sGalleryModel();
    $galleryItem->parent = $article->id;
    $galleryItem->block = '1';
    $galleryItem->position = 0;
    $galleryItem->file = 'assets/images/evo-logo.png';
    $galleryItem->type = 'image';
    $galleryItem->item_type = 'resource';
    $galleryItem->save();

    $thumb = Helper::articleThumb($article->id);

    expect($thumb)->toContain('<img')
        ->and($thumb)->toContain('assets/images/evo-logo.png')
        ->and($thumb)->toContain('max-width:400px');
});

test('creating a new article resource persists the expected fields', function () {
    $article = SiteContent::create([
        'pagetitle' => 'Brand new article',
        'description' => 'A short description',
        'introtext' => 'Intro text for the listing',
        'content' => '<p>Full article body.</p>',
        'template' => 3,
        'parent' => 2,
        'published' => 1,
    ]);

    $fromDb = SiteContent::find($article->id);

    expect($fromDb->pagetitle)->toBe('Brand new article')
        ->and($fromDb->description)->toBe('A short description')
        ->and($fromDb->introtext)->toBe('Intro text for the listing')
        ->and($fromDb->content)->toBe('<p>Full article body.</p>')
        ->and($fromDb->template)->toBe(3)
        ->and((bool) $fromDb->published)->toBeTrue();
});

test('editing an article with a partial payload does not clear untouched fields', function () {
    // This documents Eloquent's actual behavior (update() only touches keys
    // you pass it) — the data-loss the user hit came from the browser
    // submitting empty values, not from this layer clearing them.
    $article = SiteContent::create([
        'pagetitle' => 'Original title',
        'description' => 'Original description',
        'content' => '<p>Original content.</p>',
        'template' => 3,
        'parent' => 2,
        'published' => 1,
    ]);

    $article->update(['description' => 'Updated description only']);

    $fromDb = SiteContent::find($article->id);

    expect($fromDb->description)->toBe('Updated description only')
        ->and($fromDb->pagetitle)->toBe('Original title')
        ->and($fromDb->content)->toBe('<p>Original content.</p>');
});

test('SiteContentObserver blocks saving a resource with an empty pagetitle', function () {
    // Root-cause finding for the "article lost its text after adding a
    // gallery image" incident: SiteContentObserver::saving() (registered
    // via core/config/cms/observers.php) already refuses to persist an
    // empty pagetitle — save() returns false and the row is left alone.
    // So whatever cleared the article's fields in the manager did NOT go
    // through SiteContent::save()/update() as itself, otherwise this guard
    // would have caught it. This test protects that guard from regressing.
    $article = SiteContent::create([
        'pagetitle' => 'Will not be wiped',
        'template' => 3,
        'parent' => 2,
        'published' => 1,
    ]);

    $resource = SiteContent::withTrashed()->find($article->id);
    $resource->pagetitle = '';
    $result = $resource->save();

    expect($result)->toBeFalse()
        ->and(SiteContent::find($article->id)->pagetitle)->toBe('Will not be wiped');
});
