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
    $routes->get('login', 'Operateur::login');
    $routes->get('logout', 'Operateur::logout');
    $routes->post('authenticate', 'Operateur::authenticate');
    $routes->get('comptes', 'Operateur::comptes');
    $routes->get('gains', 'Operateur::gains');
    $routes->get('operations', 'Operateur::operations');
    $routes->post('addTypeOperation', 'Operateur::addTypeOperation');
    $routes->post('editTypeOperation/(:num)', 'Operateur::editTypeOperation/$1');
    $routes->get('bareme/(:num)', 'Operateur::bareme/$1');
    $routes->get('prefixes', 'Operateur::prefixes');
    $routes->post('addPrefixe', 'Operateur::addPrefixe');
    $routes->post('editPrefixe/(:num)', 'Operateur::editPrefixe/$1');
    $routes->get('deletePrefixe/(:num)', 'Operateur::deletePrefixe/$1');
    $routes->post('addBareme', 'Operateur::addBareme');
    $routes->post('editBareme/(:num)/(:num)', 'Operateur::editBareme/$1/$2');
    $routes->get('deleteBareme/(:num)/(:num)', 'Operateur::deleteBareme/$1/$2');
});
