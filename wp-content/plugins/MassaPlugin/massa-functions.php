<?php
/*
Plugin Name: MassaPlugin
Description: Adds Custom Functionality
Version: 1.0
*/

// Add a new menu item to the WordPress admin menu
function global_options_page_menu() {
  add_menu_page(
    'הגדרות כלליות - אורן', // Page title
    'הגדרות כלליות - אורן', // Menu title
    'manage_options', // Capability required to access the page
    'custom-admin-page', // Page slug
    'global_options_page_content', // Callback function to display the page content
    'dashicons-admin-page', // Icon for the menu item (you can choose from dashicons: https://developer.wordpress.org/resource/dashicons/)
    99 // Position of the menu item
  );
}
add_action('admin_menu', 'global_options_page_menu');

// Content for the custom admin page
function global_options_page_content() {
	// Check if ACF is active
  if (function_exists('acf_add_local_field_group')) {
    // Get the ACF group using the field group key
    $group = acf_get_field_group('group_64a2ce0813d29');

    // Check if the group exists
    if ($group) {
      // Get the fields for the group
      $fields = acf_get_fields($group);

      echo '<div class="wrap">';
      echo '<h1>הגדרות כלליות</h1>';

      // Check if form is submitted
      if (isset($_POST['acf_form_submit']) && $_POST['acf_form_submit'] === 'global_options_page') {
        $errors = array();

        // Loop through the submitted field values and validate them
        foreach ($fields as $field) {
          $value = $_POST[$field['name']];

          // Validate number field
          if ($field['type'] === 'number') {
            if ($value !== '' && !is_numeric($value)) {
              $errors[] = "'". $field['label'] . "'" . ' חייב להיות מספר.' ;
            }
          }

          // ... add more validation checks for other field types if needed

          // Update the field value if no validation errors
          if (empty($errors)) {
            update_field($field['name'], $value, 'options');
          }
        }

        // Display validation errors if any
        if (!empty($errors)) {
          echo '<div class="error"><p><strong>ישנם שדות שלא נשמרו - שגיאה:</strong></p><ul>';
          foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
          }
          echo '</ul></div>';
        } else {
          echo '<div class="updated"><p>עודכן בהצלחה</p></div>';
        }
      }

      // Display the form with editable fields
      echo '<form method="POST">';
      echo '<input type="hidden" name="acf_form_submit" value="global_options_page">';

      // Loop through the ACF fields and display their values
      foreach ($fields as $field) {
        $value = get_field($field['name'], 'options');
        echo '<p><label for="' . $field['name'] . '">' . $field['label'] . '</label><br>';
        echo '<input type="text" id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . $value . '"></p>';
      }

      echo '<p><input type="submit" value="שמירה"></p>';
      echo '</form>';

      echo '</div>';
    }
  } else {
    echo '<div class="wrap">';
    echo '<h1>Custom Admin Page</h1>';
    echo '<p>Please install and activate the Advanced Custom Fields plugin.</p>';
    echo '</div>';
  }
}

// Add a new menu item to the WordPress admin menu
function custom_reports_page_menu() {
  add_menu_page(
    'דוחות - אורן', // Page title
    'דוחות - אורן', // Menu title
    'manage_options', // Capability required to access the page
    'custom-reports-page', // Page slug
    'custom_reports_page_content', // Callback function to display the page content
    'dashicons-admin-page', // Icon for the menu item (you can choose from dashicons: https://developer.wordpress.org/resource/dashicons/)
    99 // Position of the menu item
  );
}
add_action('admin_menu', 'custom_reports_page_menu');

// Content for the custom admin page
function custom_reports_page_content() {
  echo '<div class="wrap">';
  echo '<h1>דוחות</h1>';
    
	// Display the links/buttons
  echo '<p><a href="?page=custom-reports-page&action=users" class="button">טבלת משתמשים</a>';
  echo '<a href="?page=custom-reports-page&action=text" class="button">סתם טקסט</a></p>';

  // Check the action parameter and display the corresponding content
  if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'users') {
      display_users();
    } elseif ($action === 'text') {
      display_text();
    }
  }
  
  echo '</div>';
}

// Function to display users
function display_users() {
  // Retrieve users
  $users = get_users();
  
  // Check if there are users
  if (!empty($users)) {
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>Name</th><th>Details</th></tr></thead>';
    echo '<tbody>';
    
    // Loop through users
    foreach ($users as $user) {
      $user_id = $user->ID;
      $user_name = $user->display_name;
      
      echo '<tr>';
      echo '<td>' . $user_id . '</td>';
      echo '<td>' . $user_name . '</td>';
      echo '<td><a href="' . admin_url('user-edit.php?user_id=' . $user_id) . '">View Details</a></td>';
      echo '</tr>';
    }
    
    echo '</tbody></table>';
  } else {
    echo '<p>No users found.</p>';
  }
}

// Function to display text
function display_text() {
  echo '<h2>Just Another Text</h2>';
  echo '<p>BaliApp</p>';
}

