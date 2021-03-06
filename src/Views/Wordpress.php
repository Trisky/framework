<?php

namespace OffbeatWP\Views;

use OffbeatWP\Contracts\SiteSettings;

class Wordpress
{
    public function head()
    {
        ob_start();
        wp_head();
        return ob_get_clean();
    }

    public function footer()
    {
        ob_start();
        wp_footer();
        return ob_get_clean();
    }

    public function title()
    {
        return wp_title('&raquo;', false);
    }

    public function languageAttributes()
    {
        return get_language_attributes();
    }

    public function archiveUrl($arg = 'post')
    {
        return get_post_type_archive_link($arg);
    }

    public function navMenu($args = [])
    {
        $args['echo'] = false;

        return wp_nav_menu($args);
    }

    public function homeUrl()
    {
        return get_home_url();
    }

    public function siteUrl()
    {
        return site_url();
    }

    public function bloginfo($name)
    {
        return get_bloginfo($name, 'display');
    }

    public function bodyClass($class = '')
    {
        return body_class($class);
    }

    public function action($action, $args = [])
    {
        ob_start();
        do_action($action, $args);
        return ob_get_clean();
    }

    public function shortcode($code)
    {
        return do_shortcode($code);
    }

    public function sidebar($name)
    {
        ob_start();
        dynamic_sidebar($name);
        return ob_get_clean();
    }

    public function attachmentUrl($attachmentID, $size = 'full')
    {
        $attachment = wp_get_attachment_image_src($attachmentID, $size);

        if (!$attachment) {
            return false;
        }

        return $attachment[0];
    }

    public function getAttachmentImage($attachmentID, $size = 'thumbnail', $classes = ['img-fluid'])
    {
        return wp_get_attachment_image($attachmentID, $size, false, ['class' => implode(' ', $classes)]);
    }

    public function formatDate($format, $date, $strtotime = false)
    {
        if ($strtotime) {
            $date = strtotime($date);
        }

        return date_i18n($format, $date);
    }

    public function resetPostdata()
    {
        wp_reset_postdata();
    }

    public function isFrontPage()
    {
        return is_front_page();
    }

    public function templateUrl($path = null)
    {
        $url = untrailingslashit(get_template_directory_uri());

        if (!is_null($path)) {
            $url .= $path;
        }

        return $url;
    }

    public function isSearchPage()
    {
        return is_search();
    }

    public function getSearchQuery()
    {
        if ($this->isSearchPage())
            return get_search_query();
    }

    public function getPageTitle()
    {
        return get_the_title();
    }
    
    public function getAllPostMeta($postId = null)
    {

        if ($postId)
            return get_post_meta($postId);

        global $post;

        if (!$post)
            return false;

        return get_post_meta($post->ID);

    }
    
}
