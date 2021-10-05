<?php

namespace Drupal\myform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ResumeForm.
 *
 * @package Drupal\myform\Form
 */
class ResumeForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'resume_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attached']['library'][] = 'myform/myform_setting';
    $conn = Database::getConnection();
    $record = array();
    if (isset($_GET['num'])) {
      $query = $conn->select('myform', 'm')
        ->condition('id', $_GET['num'])
        ->fields('m');
      $record = $query->execute()->fetchAssoc();

    }

    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name:'),
      '#required' => TRUE,
      //'#default_values' => array(array('id')),
      '#default_value' => (isset($record['first_name']) && $_GET['num']) ? $record['first_name']:'',
    );
    //print_r($form);die();

    $form['last_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Last Name:'),
      '#required' => TRUE,
      //'#default_values' => array(array('id')),
      '#default_value' => (isset($record['last_name']) && $_GET['num']) ? $record['last_name']:'',
    );

    $form['mail'] = array(
      '#type' => 'email',
      '#title' => t('Email'),
      '#default_value' => (isset($record['mail']) && $_GET['num']) ? $record['mail']:'',
    );

    $form['mob_number'] = array(
      '#type' => 'tel',
      '#title' => t('Mob No:'),
      '#required' => TRUE,
      '#default_value' => (isset($record['mob_number']) && $_GET['num']) ? $record['mob_number']:'',
    );

    $form['dob'] = array (
      '#type' => 'date',
      '#title' => t('D.O.B'),
      '#required' => TRUE,
      '#default_value' => (isset($record['dob']) && $_GET['num']) ? $record['dob']:'',
    );

    $form['gender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => array(
        'Female' => t('Female'),
        'male' => t('Male'),
        '#default_value' => (isset($record['gender']) && $_GET['num']) ? $record['gender']:'',
      ),
    );

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      //'#value' => t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

      if (!preg_match ("/^[a-zA-z]*$/", $form_state->getValue('first_name')) ) {  
        $form_state->setErrorByName('candidate_name', $this->t('Name is invalid.'));  
      }

      if (!preg_match ("/^[a-zA-z]*$/", $form_state->getValue('last_name')) ) {  
        $form_state->setErrorByName('Last_name', $this->t('Name is invalid.'));  
      }

      $dateOfBirth = ($form_state->getValue('dob'));
 
      // Get today's date
      $today =  date("Y-m-d");
  
      // Calculate the time difference between the two dates
      $diff = date_diff(date_create($dateOfBirth), date_create($today));

      if ($diff->y<18) {
        $form_state->setErrorByName('DOB', $this->t('Your age is less than 18.'));
      }

      if (strlen($form_state->getValue('mob_number')) < 10) {
        $form_state->setErrorByName('mobile', $this->t('Mobile number is too short.'));
      }

      parent::validateForm($form, $form_state);

    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $field=$form_state->getValues();
    $name=$field['first_name'];
    $lname=$field['last_name'];
    $email=$field['mail'];
    $number=$field['mob_number'];
    $gender=$field['gender'];
    $dob=$field['dob'];

    /*$insert = array('name' => $name, 'mobilenumber' => $number, 'email' => $email, 'age' => $age, 'gender' => $gender, 'website' => $website);
    db_insert('mydata')
    ->fields($insert)
    ->execute();
    if($insert == TRUE)
    {
      drupal_set_message("your application subimitted successfully");
    }
    else
    {
      drupal_set_message("your application not subimitted ");
    }*/

    if (isset($_GET['num'])) {
      $field  = array(
        'first_name'   => $name,
        'last_name' =>  $lname,
        'mail' =>  $email,
        'dob' => $dob,
        'gender' => $gender,
        'mob_number' => $number,
      );
      $query = \Drupal::database();
      $query->update('myform')
        ->fields($field)
        ->condition('id', $_GET['num'])
        ->execute();
      drupal_set_message("succesfully updated");
      $form_state->setRedirect('myform.displaycontroller');

    }

    else
    {
      $field  = array(
        'first_name'   =>  $name,
        'last_name' =>  $lname,
        'mail' =>  $email,
        'dob' => $dob,
        'gender' => $gender,
        'mob_number' => $number,
      );
      $query = \Drupal::database();
      $query ->insert('myform')
        ->fields($field)
        ->execute();
      drupal_set_message("succesfully saved");
      $form_state->setRedirect('myform.displaycontroller');
   
    }
  }

}
