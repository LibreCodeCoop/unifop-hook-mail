<?php
/**
 * Plugin Name:       Unifop hook mail
 * Description:       Intercepts the email sending after payment confirmation and inserts a link to UNIFOP's Jitsi. 
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            LibreCode
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */


add_filter('wp_mail','redirect_mails', 10,1);
function redirect_mails($args){
    if ($args["subject"] == "Seu pedido em Unifop foi concluído") {
        $args["message"] = str_replace('{link_consulta}','https://link',$args["message"]);
    }
    return $args;
}