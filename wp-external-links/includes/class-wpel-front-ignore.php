<?php
/**
 * Class WPEL_Front_Ignore
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.0.4
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Front_Ignore extends WPRun_Base_1x0x0
{

    /**
     * @var array
     */
    private $content_placeholders = array();

    /**
     * @var WPEL_Settings_Page
     */
    private $settings_page = null;

    /**
     * Initialize
     * @param WPEL_Settings_Page $settings_page
     */
    protected function init( WPEL_Settings_Page $settings_page )
    {
        $this->settings_page = $settings_page;
    }

    /**
     * Get option value
     * @param string $key
     * @param string|null $type
     * @return string
     * @triggers E_USER_NOTICE Option value cannot be found
     */
    protected function opt( $key, $type = null )
    {
        return $this->settings_page->get_option_value( $key, $type );
    }

    /**
     * Filter for "wpel_apply_link"
     * @param WPEL_Link $link
     * @return boolean
     */
    protected function filter_wpel_apply_link_10000000000( WPEL_Link $link )
    {
        // has ignore flag
        if ( $link->isIgnore() ) {
            return false;
        }

        // ignore mailto links
        if ( $this->is_mailto( $link->getAttribute( 'href' ) ) ) {
            return false;
        }

        // ignore WP Admin Bar Links
        if ( $link->hasAttributeValue( 'class', 'ab-item' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Filter for "wpel_before_filter"
     * @param string $content
     * @return string
     */
    protected function filter_wpel_before_filter_10000000000( $content )
    {
        $ignore_tags = array( 'head' );

        if ( $this->opt( 'ignore_script_tags' ) ) {
            $ignore_tags[] = 'script';
        }

        foreach ( $ignore_tags as $tag_name ) {
            $content = preg_replace_callback(
                $this->get_tag_regexp( $tag_name )
                , $this->get_callback( 'skip_tag' )
                , $content
            );
        }

        return $content;
    }

    /**
     * @param type $tag_name
     * @return type
     */
    protected function get_tag_regexp( $tag_name )
    {
        return '/<'. $tag_name .'[\s.*>|>](.*?)<\/'. $tag_name .'[\s+]*>/is';
    }

    /**
     * Filter for "wpel_after_filter"
     * @param string $content
     * @return string
     */
    protected function filter_wpel_after_filter_10000000000( $content )
    {
       return $this->restore_content_placeholders( $content );
    }

    /**
     * Pregmatch callback
     * @param array $matches
     * @return string
     */
    protected function skip_tag( $matches )
    {
        $skip_content = $matches[ 0 ];
        return $this->get_placeholder( $skip_content );
    }

    /**
     * Return placeholder text for given content
     * @param string $placeholding_content
     * @return string
     */
    protected function get_placeholder( $placeholding_content )
    {
        $placeholder = '<!--- WPEL PLACEHOLDER '. count( $this->content_placeholders ) .' --->';
        $this->content_placeholders[ $placeholder ] = $placeholding_content;
        return $placeholder;
    }

    /**
     * Restore placeholders with original content
     * @param string $content
     * @return string
     */
    protected function restore_content_placeholders( $content )
    {
        foreach ( $this->content_placeholders as $placeholder => $placeholding_content ) {
            $content = str_replace( $placeholder, $placeholding_content, $content );
        }

        return $content;
    }

    /**
     * Check url is mailto link
     * @param string $url
     * @return boolean
     */
    protected function is_mailto( $url )
    {
        if ( substr( trim( $url ), 0, 7 ) === 'mailto:' ) {
            return true;
        }

        return false;
    }

}

/*?>*/
