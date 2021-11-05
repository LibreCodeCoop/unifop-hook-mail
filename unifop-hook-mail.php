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

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

function generateJitsiUrl($userName, $userEmail, $roomName, $moderator)
{
    $config = Configuration::forSymmetricSigner(
        new Sha256(),
        InMemory::plainText(getenv('JITSI_SECRET'))
    );

    $token = $config->Builder()
        ->permittedFor(getenv('JITSI_APPID')) //aud
        ->issuedBy(getenv('JITSI_APPID')) // iss
        ->relatedTo(getenv('JITSI_URL')) // sub
        ->withClaim('room', $roomName) // room
        ->withClaim('moderator', $moderator) // moderator
        ->withClaim('context', [
            'user' => [
                'name' => $userName,
                'email' => $userEmail
                ]
        ]) // room
        ->getToken($config->signer(), $config->signingKey());
    return 'https://' . getenv('JITSI_URL') .
        '/' . $roomName.
        '?jwt=' . $token->toString();
}

add_filter('wp_mail','insertLink', 10,1);
function insertLink($args){
    if ($args["subject"] == "Seu pedido em Unifop foi concluído") {
        $nome = strtok($args["to"], '@');
        $url = generateJitsiUrl($nome, $args["to"], 'room-name', false);
        $args["message"] = str_replace('{link_consulta}',"<a href='$url'>Link da consulta</a>",$args["message"]);
    }
    return $args;
}
