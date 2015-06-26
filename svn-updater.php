<?php
/*
Plugin Name: SVN Updater
Version: 0.1.0
Description: Install any WordPress.org plugin's trunk version from SVN.
Tags: administration, installation, update, updater, svn, trunk, plugins
Plugin URI: https://wordpress.org/plugins/svn-updater/
Author: Viktor Szépe
Author URI: http://www.online1.hu/webdesign/
License: GNU General Public License (GPL) version 2
GitHub Plugin URI: https://github.com/szepeviktor/svn-updater
*/

if ( ! function_exists( 'add_filter' ) ) {
    ob_get_level() && ob_end_clean();
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

class O1_SVN_Updater {

    private $plugin;

    public function __construct() {

        add_filter( 'plugin_action_links', array( $this, 'plugin_link' ), 10, 4 );
        add_action( 'update-custom_' . 'svn-update-plugin', array( $this, 'svn_update_plugin' ) );
    }

    public function plugin_link( $actions, $plugin_file, $plugin_data, $context ) {

        if ( ! in_array( $context, array( 'search', 'mustuse', 'dropins' ) ) ) {
            $plugin_state = get_site_transient( 'update_plugins' );
            if ( isset( $plugin_state->response[ $plugin_file ] )
               || isset( $plugin_state->no_update[ $plugin_file ] )
            ) {
                $update_url = wp_nonce_url(
                    self_admin_url( 'update.php?action=svn-update-plugin&plugin=' . $plugin_file ),
                    'svn_update_plugin'
                );
                $actions['trunk'] = sprintf( '<a href="%s">Trunk</a>', $update_url );
            }
        }

        return $actions;
    }

    public function svn_update_plugin() {

        global $title, $parent_file, $submenu_file;

        if ( ! current_user_can( 'update_plugins' ) )
            wp_die( __( 'You do not have sufficient permissions to update plugins for this site.' ) );

        check_admin_referer( 'svn_update_plugin' );

        $this->plugin = $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        if ( empty( $plugin ) ) {
            wp_die( 'Plugin name is missing.' );
        }

        add_filter( 'site_transient_update_plugins', array( $this, 'rewrite_update_plugins_url' ) );

        $title = __( 'Update Plugin' );
        $parent_file = 'plugins.php';
        $submenu_file = 'plugins.php';

        wp_enqueue_script( 'updates' );
        require_once( ABSPATH . 'wp-admin/admin-header.php' );

        $nonce = 'upgrade-plugin_' . $plugin;
        $url = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin );

        $upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin' ) ) );
        $upgrader->upgrade( $plugin );

        include( ABSPATH . 'wp-admin/admin-footer.php' );
    }

    public function rewrite_update_plugins_url( $transient ) {

        $trunk_zip_url_template = 'https://downloads.wordpress.org/plugin/%s.zip';

        if ( ! isset( $transient->response[ $this->plugin ] )
            && isset( $transient->no_update[ $this->plugin ] )
        ) {
            $transient->response[ $this->plugin ] = $transient->no_update[ $this->plugin ];
        }
        if ( isset( $transient->response[ $this->plugin ] ) ) {
            $slug = $transient->response[ $this->plugin ]->slug;
            $transient->response[ $this->plugin ]->package = sprintf( $trunk_zip_url_template, $slug );
        }

        return $transient;
    }
}

new O1_SVN_Updater();
