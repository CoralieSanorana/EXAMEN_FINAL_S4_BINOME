<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route - redirect to client login
$routes->get('/', 'Client::index');

// Client routes - grouped and secured
$routes->group('client', function($routes) {
    // Public routes (login/logout)
    $routes->get('login', 'Client::login');
    $routes->post('authenticate', 'Client::authenticate');
    $routes->get('logout', 'Client::logout');
    
    // Protected routes (require authentication)
    $routes->get('solde', 'Client::solde', ['filter' => 'auth']);
    $routes->get('depot', 'Client::depot', ['filter' => 'auth']);
    $routes->post('processDepot', 'Client::processDepot', ['filter' => 'auth']);
    $routes->get('retrait', 'Client::retrait', ['filter' => 'auth']);
    $routes->post('processRetrait', 'Client::processRetrait', ['filter' => 'auth']);
    $routes->get('transfert', 'Client::transfert', ['filter' => 'auth']);
    $routes->post('processTransfert', 'Client::processTransfert', ['filter' => 'auth']);
    $routes->get('rechercherClient', 'Client::rechercherClient', ['filter' => 'auth']);
    $routes->get('historique', 'Client::historique', ['filter' => 'auth']);
    $routes->get('profil', 'Client::profil', ['filter' => 'auth']);
    $routes->post('updateProfil', 'Client::updateProfil', ['filter' => 'auth']);
});

// Operateur routes - grouped
$routes->group('operateur', function($routes) {
    // Public routes (login/logout)
    $routes->get('login', 'Operateur::login');
    $routes->get('logout', 'Operateur::logout');
    $routes->post('authenticate', 'Operateur::authenticate');
    
    // Protected routes (require authentication)
    $routes->get('comptes', 'Operateur::comptes', ['filter' => 'auth']);
    $routes->get('gains', 'Operateur::gains', ['filter' => 'auth']);
    $routes->get('operations', 'Operateur::operations', ['filter' => 'auth']);
    $routes->post('addTypeOperation', 'Operateur::addTypeOperation', ['filter' => 'auth']);
    $routes->post('editTypeOperation/(:num)', 'Operateur::editTypeOperation/$1', ['filter' => 'auth']);
    $routes->get('bareme/(:num)', 'Operateur::bareme/$1', ['filter' => 'auth']);
    $routes->get('prefixes', 'Operateur::prefixes', ['filter' => 'auth']);
    $routes->post('addPrefixe', 'Operateur::addPrefixe', ['filter' => 'auth']);
    $routes->post('editPrefixe/(:num)', 'Operateur::editPrefixe/$1', ['filter' => 'auth']);
    $routes->get('deletePrefixe/(:num)', 'Operateur::deletePrefixe/$1', ['filter' => 'auth']);
    $routes->post('addBareme', 'Operateur::addBareme', ['filter' => 'auth']);
    $routes->post('editBareme/(:num)/(:num)', 'Operateur::editBareme/$1/$2', ['filter' => 'auth']);
    $routes->get('deleteBareme/(:num)/(:num)', 'Operateur::deleteBareme/$1/$2', ['filter' => 'auth']);
});
