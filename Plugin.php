<?php namespace Inetis\RicheditorSnippets;

use Event;
use System\Classes\PluginBase;
use Backend\FormWidgets\RichEditor;
use Backend\Classes\Controller;
use Inetis\RicheditorSnippets\Classes\SnippetParser;
use RainLab\Pages\Controllers\Index as StaticPage;
use Inetis\RicheditorSnippets\Classes\SnippetLoader;

/**
 * RicheditorSnippets Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Pages'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Richeditor Snippets',
            'description' => 'Adds button to Richeditor toolbar to quickly add Snippets.',
            'author'      => 'Tough Developer & inetis',
            'icon'        => 'icon-newspaper-o',
            'homepage'    => 'https://github.com/inetis-ch/oc-richeditorsnippets-plugin'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        // Extend controllers to always have Static Page methods
        Controller::extend(function($widget) {
            $widget->addDynamicMethod('onGetInspectorConfiguration', function() {
                return (new StaticPage)->onGetInspectorConfiguration();
            });

            $widget->addDynamicMethod('onGetSnippetNames', function() {
                return (new StaticPage)->onGetSnippetNames();
            });

            $widget->addDynamicMethod('onInspectableGetOptions', function() {
                return (new StaticPage)->onInspectableGetOptions();
            });
        });

        RichEditor::extend(function($widget) {
            // Adds default CSS/JS for snippets from Rainlab Pages Plugin
            $widget->addCss('/plugins/rainlab/pages/assets/css/pages.css');
            $widget->addJs('/plugins/rainlab/pages/assets/js/pages-page.js');
            $widget->addJs('/plugins/rainlab/pages/assets/js/pages-snippets.js');

            // Adds custom javascript
            $widget->addJs('/inetis/snippets/list');
            $widget->addJs('/plugins/inetis/richeditorsnippets/assets/js/froala.snippets.plugin.js');
        });

        // Register components from cache for AJAX handlers
        Event::listen('cms.page.initComponents', function($controller, $page) {
            SnippetLoader::restoreComponentSnippetsFromCache($controller, $page);
        });
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'parseSnippets' => function($html) {
                    return SnippetParser::parse($html);
                }
            ]
        ];
    }
}
