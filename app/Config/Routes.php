<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route - redirect to client login
$routes->get('/', 'Client::index');

// Client routes - grouped and secured
$routes->group('client', ['filter' => 'nocache'], function($routes) {
    // Public routes (login/logout)
    $routes->get('login', 'Client::login');
    $routes->post('authenticate', 'Client::authenticate');
    $routes->get('logout', 'Client::logout');
    
    // Protected routes (require authentication)
    $routes->get('solde', 'Client::solde');
    $routes->get('depot', 'Client::depot');
    $routes->post('processDepot', 'Client::processDepot');
    $routes->get('retrait', 'Client::retrait');
    $routes->post('processRetrait', 'Client::processRetrait');
    $routes->get('transfert', 'Client::transfert');
    $routes->post('processTransfert', 'Client::processTransfert');
    $routes->get('rechercherClient', 'Client::rechercherClient');
    $routes->get('historique', 'Client::historique');
    $routes->get('profil', 'Client::profil');
    $routes->post('updateProfil', 'Client::updateProfil');
});

// Operateur routes - grouped
$routes->group('operateur', function($routes) {
    $routes->get('comptes', 'Operateur::comptes');
    $routes->get('gains', 'Operateur::gains');
    $routes->get('operations', 'Operateur::operations');
    $routes->get('prefixes', 'Operateur::prefixes');
});
