<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller routines for page json routes.
 */
class CustomApiController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'custom_api';
  }

  /**
   *
   * This callback is mapped to the path
   * 'page_json/{apikey}/{nid}'.
   *
   * @param string $apikey
   *   A string to use, should be match with "siteapikey".
   * @param string $nid
   *   Another string to use, should be a number.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   If the parameters are invalid.
   */
  public function jsonview($apikey, $nid) {
	
	//drupal_set_title(t('JSON Preview'));
	
	$config 		= \Drupal::config('system.site');
	$config_api_key = $config->get('siteapikey');
	
	// if api key is not matched and nid is not numeric, show the "access denied" page
    if ($config_api_key != $apikey || !is_numeric($nid)) {
      throw new AccessDeniedHttpException();
    }

	// load node data
    $node_details = Node::load($nid);
	
	// if node is not exist, show the "access denied" page
	if (empty($node_details)) {
      throw new AccessDeniedHttpException();
    }
	
	// make an array
	$json_data = array();
	$json_data['nid'] 				= $node_details->nid->value;
	$json_data['type'] 				= $node_details->type->target_id;
	$json_data['langcode'] 			= $node_details->langcode->value;
	$json_data['default_langcode'] 	= $node_details->default_langcode->value;
	$json_data['title'] 			= $node_details->title->value;
	$json_data['body'] 				= $node_details->body->value;
	$json_data['sticky'] 			= $node_details->sticky->value;
	$json_data['promote'] 			= $node_details->promote->value;
	$json_data['uid'] 				= $node_details->uid->target_id;
	$json_data['status'] 			= $node_details->status->value;
	$json_data['created'] 			= $node_details->created->value;
	$json_data['changed'] 			= $node_details->changed->value;

	// return the node data
    return array(
      '#markup' => "<pre>".json_encode($json_data, JSON_PRETTY_PRINT)."</pre>",
    );
  }

}
