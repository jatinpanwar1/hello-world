<?php
namespace Drupal\myform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Class DisplayTableController.
 *
 * @package Drupal\mydata\Controller
 */
class DisplayTableController extends ControllerBase {
  /**
   * Display.
   *
   * @return string
   *   Return Hello string.
   */
  public function display() {
    /**return [
    '#type' => 'markup',
    '#markup' => $this->t('Implement method: display with parameter(s): $name'),
    ];*/

    //create table header
    $header_table = array(
      'id'=>    t('Sr.No'),
      'first_name' => t('First_Name'),
      'last_name' => t('Last_Name'),
      'email' => t('Mail'),
      'mobile' => t('Number'),
      'dob' => t('D.O.B'),
      'gender' => t('Gender'),
      'edit1' => t('Edit'),
      'delete' => t('Delete'),
    );

//select records from table
    $query = \Drupal::database()->select('myform', 'm');
    $query->fields('m',['id','first_name','last_name','mail','mob_number','dob','gender']);
    $results = $query->execute()->fetchAll();
    $rows=array();
    foreach($results as $data){
      $delete = Url::fromUserInput('/myform/form/delete/'.$data->id);
      $edit   = Url::fromUserInput('/rs/myform?num='.$data->id);

      //print the data from table
      $rows[] = array(
        'id' =>$data->id,
        'first_name' => $data->first_name,
        'last_name' => $data->last_name,
        'mail' =>$data->mail,
        'mob_number' => $data->mob_number,
        'dob' => $data->dob,
        'gender' => $data->gender,
       
        \Drupal::l('Edit', $edit),
        \Drupal::l('Delete', $delete),
      );

    }
    //display data in site
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No users found'),
    ];
//        echo '<pre>';print_r($form['table']);exit;
    return $form;

  }

}
