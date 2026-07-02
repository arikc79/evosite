<?php namespace EvolutionCMS\Main;

use EvolutionCMS\ServiceProvider;
use Illuminate\Support\Facades\Event;

class MainServiceProvider extends ServiceProvider
{
    /**
     * Если указать пустую строку, то сниппеты и чанки будут иметь привычное нам именование
     * Допустим, файл test создаст чанк/сниппет с именем test
     * Если же указан namespace то файл test создаст чанк/сниппет с именем main#test
     * При этом поддерживаются файлы в подпапках. Т.е. файл test из папки subdir создаст элемент с именем subdir/test
     */
    protected $namespace = 'main';
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /*$this->loadSnippetsFrom(
            dirname(__DIR__). '/snippets/',
            $this->namespace
        );*/
        /*$this->loadChunksFrom(
            dirname(__DIR__) . '/chunks/',
            $this->namespace
        );*/
        /*$this->loadPluginsFrom(
            dirname(__DIR__) . '/plugins/'
        );*/
        //use this code for each module what you want add
        /*$this->app->registerModule(
            'module from file',
            dirname(__DIR__).'/modules/module.php'
        );*/

        // Blade templates skip the legacy tag parser (settings, links, chunks,
        // snippets), so resolve those tags here for pages rendered via views/*.blade.php.
        Event::listen('evolution.OnWebPagePrerender', function ($params) {
            $params['documentOutput'] = evo()->parseDocumentSource($params['documentOutput']);
            $params['documentOutput'] = evo()->cleanUpMODXTags($params['documentOutput']);
            $params['documentOutput'] = evo()->rewriteUrls($params['documentOutput']);
        });
    }
}