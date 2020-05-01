<?php

use Drupal\civicrm_entity\SupportedEntities;

Class CRM_Migrate_Geolocation {

  public $civicrmPath = '/var/www/jma.staging.autismontario.com/htdocs/vendor/civicrm/civicrm-core/';

  function __construct() {
    // you can run this program either from an apache command, or from the cli
    $this->initialize();
  }

  function initialize() {
    ini_set('memory_limit', -1);
    $civicrmPath = $this->civicrmPath;
    require_once $civicrmPath . 'civicrm.config.php';
    require_once $civicrmPath . 'CRM/Core/Config.php';
    $config = CRM_Core_Config::singleton();
    \Drupal::service('civicrm')->initialize();
  }

  function migrateGeoLocation($entity == 'Contact', $limit = 100) {
    $dao = CRM_Core_DAO::executeQuery("
     SELECT a.id as address_id, e.id as contact_id
       FROM civicrm_contact e
        INNER JOIN  civicrm_address a ON a.contact_id = e.id AND a.is_primary = 1
        WHERE contact_sub_type LIKE '%service_provider%'
        LIMIT 0, $limit
    ");
    while($dao->fetch()) {
     $addressID = $dao->address_id;
     if (!empty($addressID)) {
       $address = new CRM_Core_BAO_Address();
       $address->id = $addressID;
       $address->find(TRUE);
       if (!empty($address->geo_code_1) && !empty($address->geo_code_2)) {
         $entityType = SupportedEntities::getEntityType($entity);
         $key = ($entity == 'Contact') ? 'field_mapped_location' : 'field_mapped_location';
         $entity = \Drupal::entityTypeManager()->getStorage(SupportedEntities::getEntityType($entity))->load($entityID);
         $params = [
           'lat' => $address->geo_code_1,
           'lng'=> $address->geo_code_2,
           'lat_sin' => sin(deg2rad($address->geo_code_1)),
           'lat_cos' => cos(deg2rad($address->geo_code_1)),
           'lng_rad' => deg2rad($address->geo_code_2),
         ];
         $params['data'] = $params;
         $entity->get('field_geolocation')->setValue(array($params));
         $entity->get($key)->setValue(1);
         $entity->save();
       }
     }

     $index = \Drupal\search_api\Entity\Index::load('default');
     $db_query = \Drupal::database()->select('search_api_item', 'sai');
     $entity_id_field = $db_query->addField('sai', 'item_id');
     $db_query->condition('sai.datasource', 'entity:civicrm_contact');
     $db_query->condition('sai.item_id', 'entity:civicrm_contact/' . $dao->contact_id . ':und');
     $results = $db_query->execute()->fetchAll();
     if (empty($results)) {
      $index->trackItemsInserted('entity:civicrm_contact', [$dao->contact_id . ':und']);
     }
     else {
      $index->trackItemsUpdated('entity:civicrm_contact', [$dao->contact_id . ':und']);
     }
   }
}

$import = new CRM_Migrate_Geolocation();
$import->migrateGeoLocation();
