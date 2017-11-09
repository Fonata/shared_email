<?php

/**
 * @file
 * Contains \Drupal\shared_email\Tests\SharedEmailTest.
 */


namespace Drupal\shared_email\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\user\RoleInterface;
use Drupal\user\Entity\Role;

/**
 * Tests for the shared email module.
 *
 * @group SharedEmail
 */
class SharedEmailTest extends WebTestBase {

  // A simple user
  private $user;

  public static $modules = array("shared_email");

  // Perform initial setup tasks that run before every test method.
  public function setUp() {
    parent::setUp();
    $this->user = $this->DrupalCreateUser(
      array('administer users', 'administer site configuration','access shared_email_message'), NULL, FALSE);
  }

  /*
   * Test that a non-duplicate email does not display the warning message
   */

  public function testNonDuplicateEmail() {
    // Login
    $this->drupalLogin($this->user);


    $this->config('user.settings')
      ->set('verify_mail', FALSE)
      ->set('register', USER_REGISTER_VISITORS)
      ->save();

    // Set up a user to check for duplicates.
    $duplicate_user = $this->drupalCreateUser();

    $edit = array();
    $name = $this->randomMachineName();
    $edit['name'] = $name;
    $edit['mail'] = $this->randomMachineName() . $duplicate_user->getEmail();
    $edit['pass[pass1]'] = 'Test1Password';
    $edit['pass[pass2]'] = 'Test1Password';


    // Attempt to create a new account using an unique email address.
    $this->drupalPostForm('admin/people/create', $edit, t('Create new account'));

    $this->assertText("Created a new user account for $name. No email has been sent", 'Verifying that standard message is displayed.');
    $config = $this->config('shared_email.settings');
    $this->assertNoText($config->get('sharedemail_msg'), "Verifying that a non-duplicate email does not display the warning message.");
  }

  /*
   * Test that a duplicate email is allowed
   */
  public function testAllowsDuplicateEmail() {
    // Login

    $this->drupalLogin($this->user);

    $this->config('user.settings')
      ->set('verify_mail', FALSE)
      ->set('register', USER_REGISTER_VISITORS)
      ->save();

    // Set up a user to check for duplicates.
    $duplicate_user = $this->drupalCreateUser();

    $edit = array();
    $name = t($this->randomMachineName());
    $edit['name'] = $name;
    $edit['mail'] = $duplicate_user->getEmail();
    $edit['pass[pass1]'] = 'Test1Password';
    $edit['pass[pass2]'] = 'Test1Password';


    // Attempt to create a new account using an existing email address.
    $this->drupalPostForm('admin/people/create', $edit, t('Create new account'));

    $config = $this->config('shared_email.settings');

    $this->assertText("Created a new user account for $name. No email has been sent", 'Verifying original message is still displayed.');
    $this->assertText(t($config->get('sharedemail_msg')), 'Verifying that a duplicate email displays the warning message.');

  }

  /*
     * Test that a duplicate email is allowed, but the user does not have
     * access to the message
     */
  public function testAllowsDuplicateEmail_noMessage() {
    // Login
    $this->user = $this->DrupalCreateUser(
      array('administer users', 'administer site configuration'), NULL, FALSE);

    $this->drupalLogin($this->user);

    $this->config('user.settings')
      ->set('verify_mail', FALSE)
      ->set('register', USER_REGISTER_VISITORS)
      ->save();

    // Set up a user to check for duplicates.
    $duplicate_user = $this->drupalCreateUser();

    $edit = array();
    $name = t($this->randomMachineName());
    $edit['name'] = $name;
    $edit['mail'] = $duplicate_user->getEmail();
    $edit['pass[pass1]'] = 'Test1Password';
    $edit['pass[pass2]'] = 'Test1Password';


    // Attempt to create a new account using an existing email address.
    $this->drupalPostForm('admin/people/create', $edit, t('Create new account'));

    $config = $this->config('shared_email.settings');

    $this->assertNoText($config->get('sharedemail_msg'), "Verifying that a non-duplicate email does not display the warning message.");
  }

  /*
   * Test the configuration form
   */
  public function testConfigForm() {
    // Login
    $this->drupalLogin($this->user);

    $this->drupalGet('/admin/config/shared_email');
    $this->assertResponse(200);

    $config = $this->config('shared_email.settings');

    $this->assertFieldByName(
      'sharedemail_msg',
      $config->get('sharedemail_msg'),
      'Source text field has the default value'
    );
    // Post the form
    $this->drupalPostForm('/admin/config/shared_email', array(
      'sharedemail_msg' => 'Test message',
    ), t('Save configuration'));
    $this->assertText(
      'The configuration options have been saved.',
      'The form was saved correctly.'
    );


    // Test the new values are there.
    $this->drupalGet('/admin/config/shared_email');
    $this->assertResponse(200);
    $this->assertFieldByName(
      'sharedemail_msg',
      'Test message',
      'Shared email message is OK.'
    );


  }

}
