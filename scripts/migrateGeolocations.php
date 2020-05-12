<?php

use Drupal\civicrm_entity\SupportedEntities;
use Drupal\Core\Entity\EntityStorageException;

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

  public function migrateGeoLocation($entity = 'Contact', $limit = 100) {
    if ($entity == 'Contact') {
      $this->migrateGeoLocationForContact($limit);
    }
    else {
      $this->migrateGeoLocationForEvent($limit);
    }
  }

  public function migrateGeoLocationForEvent($limit = 100) {
    $dao = CRM_Core_DAO::executeQuery("
      SELECT e.id as event_id, geo_code_1, geo_code_2
        FROM civicrm_event e
         INNER JOIN civicrm_loc_block lb ON lb.id = e.loc_block_id
         INNER JOIN  civicrm_address a ON a.id = lb.address_id
         WHERE geo_code_1 IS NOT NULL AND geo_code_2 IS NOT NULL
      LIMIT 0, $limit
    ");
    while($dao->fetch()) {
      $params = [
        [
          'lat' => $dao->geo_code_1,
          'lng'=> $dao->geo_code_2,
          'lat_sin' => sin(deg2rad($dao->geo_code_1)),
          'lat_cos' => cos(deg2rad($dao->geo_code_1)),
          'lng_rad' => deg2rad($dao->geo_code_2),
        ],
      ];
      $params['data'] = $params;
      $entityType = SupportedEntities::getEntityType('Event');
      $entityObj = \Drupal::entityTypeManager()->getStorage(SupportedEntities::getEntityType('Event'))->load($dao->event_id);
      $entityObj->get('field_geolocation')->setValue(array($params));
      $entityObj->get('field_mapped_location')->setValue(1);
      $entityObj->save();
    }
  }

  public function migrateGeoLocationForContact($limit = 100) {
    $dao = CRM_Core_DAO::executeQuery("
     SELECT GROUP_CONCAT(DISTINCT a.id) as address_id, e.id as contact_id
       FROM civicrm_contact e
        INNER JOIN  civicrm_address a ON a.contact_id = e.id AND geo_code_1 IS NOT NULL AND geo_code_2 IS NOT NULL
        WHERE contact_sub_type LIKE '%service_provider%'
        GROUP BY a.contact_id
        LIMIT 0, $limit
    ");
    while($dao->fetch()) {
      $addressIDs = (array) explode(',', $dao->address_id);
      $params = [];
      foreach ($addressIDs as $addressID) {
        $address = new CRM_Core_BAO_Address();
        $address->id = $addressID;
        $address->find(TRUE);

        $p = [
          'lat' => $address->geo_code_1,
          'lng'=> $address->geo_code_2,
          'lat_sin' => sin(deg2rad($address->geo_code_1)),
          'lat_cos' => cos(deg2rad($address->geo_code_1)),
          'lng_rad' => deg2rad($address->geo_code_2),
          ];
          $p['data'] = $p;
          $params[] = $p;
        }
        $entityType = SupportedEntities::getEntityType('Contact');
        $entityObj = \Drupal::entityTypeManager()->getStorage(SupportedEntities::getEntityType('Contact'))->load($dao->contact_id);
        $entityObj->get('field_geolocation')->setValue(array($params));
        $entityObj->get('field_mapped_location')->setValue(1);
        $entityObj->save();
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
