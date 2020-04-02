<?php
namespace OffbeatWP\Wordpress;

class WordpressService
{
    public $bindings = [
        'admin-page' => \OffbeatWP\Support\Wordpress\AdminPage::class,
        'ajax'       => \OffbeatWP\Support\Wordpress\Ajax::class,
        'rest-api'   => \OffbeatWP\Support\Wordpress\RestApi::class,
        'console'    => \OffbeatWP\Support\Wordpress\Console::class,
        'hooks'      => \OffbeatWP\Support\Wordpress\Hooks::class,
        'post-type'  => \OffbeatWP\Support\Wordpress\PostType::class,
        'post'       => \OffbeatWP\Support\Wordpress\Post::class,
        'page'       => \OffbeatWP\Support\Wordpress\Page::class,
        'taxonomy'   => \OffbeatWP\Support\Wordpress\Taxonomy::class,
        'design'     => \OffbeatWP\Support\Wordpress\Design::class,
    ];

    public function register()
    {
        $this->registerMenus();
        $this->registerImageSizes();
        $this->registerSidebars();

        add_filter('style_loader_tag', [$this, 'deferStyles'], 10, 4);

        // Page Template
        add_action('init', [$this, 'registerPageTemplate'], 99);
        add_filter('offbeatwp/controller/template', [$this, 'applyPageTemplate']);
    }

    public function registerMenus()
    {
        $menus = config('menus');

        if (is_object($menus) && $menus->isNotEmpty()) {
            register_nav_menus($menus->toArray());
        }
    }

    public function registerImageSizes()
    {
        $images = config('images');

        if (is_object($images) && $images->isNotEmpty()) {
            $images->each(function ($image, $key) {
                add_image_size($key, $image['width'], $image['height'], $image['crop']);
            });
        }
    }

    public function registerSidebars()
    {
        $sidebars = config('sidebars');

        if (is_object($sidebars) && $sidebars->isNotEmpty()) {
            $sidebars->each(function ($sidebar, $id) {
                $sidebar['id'] = $id;
                register_sidebar($sidebar);
            });
        }
    }

    public function deferStyles($tag, $handle, $href, $media)
    {
        if ($handle == 'wp-block-library') {
            $tag = str_replace('rel=\'stylesheet\'', 'rel=\'preload\'', $tag);
        }

        return $tag;
    }

    public function registerPageTemplate()
    {
        add_filter('theme_page_templates', function ($postTemplates) {
            $pageTemplates = offbeat('page')->getPageTemplates();

            if (is_array($pageTemplates)) {
                $postTemplates = array_merge($postTemplates, $pageTemplates);
            }

            return $postTemplates;
        }, 10, 1);
    }

    public function applyPageTemplate($template)
    {
        if (is_singular('page')) {
            $pageTemplate = get_post_meta(get_the_ID(), '_wp_page_template', true);
            if (!empty($pageTemplate) && $pageTemplate != 'default') {
                return $pageTemplate;
            }
        }

        return $template;
    }
}
