<?php
/**
 * Plugin Name: PDB custom Florain
 * Description: load the registered 'Marchés' into a Participants Database form dropdown
 */

/* 
 * sets our function to be called when the pdb-form_element_build_multi-dropdown action 
 * is triggered by the form
 *
 * if your field is not a dropdown, change the action so it is triggered on the correct 
 * element, for example if the field is a multiselect checkbox, the action would be 
 * 'pdb-form_element_build_multi-checkbox'
 */
add_action( 'pdb-form_element_build_multi-dropdown', 'florain_set_marches_dropdown_options');
/**
 * sets the options for the 'Marchés' dropdown
 *
 * @param PDb_FormElement object $field the current field
 */
function florain_set_marches_dropdown_options ( $field )
{
  if ( $field->name === 'présence_sur_les_marchés' ) :  // check for our dropdown field
  
  global $wpdb; // grab the db helper object

  /*
   * For multiselect fields, values that don't match the defined values for the 
   * field are dumped into the "other" element of the value array. In this case, 
   * we take the "other" value and make it into an array and add it to the main 
   * array of values so that they will show as selected in the form element.
   */
  if ( $field->is_multi( $field->form_element ) && isset( $field->value['other'] ) ) {
    $field->value =  array_merge( (array) $field->value, explode(',', $field->value['other'] ) );
  }
  
  /*
   * define the query for getting the list of volunteer names
   * 
   * note that the $wpdb->prefix method is used to get the table 
   * prefix; this is so it will work on all WP installs
   */
  $query = '
    SELECT titre 
    FROM `' . $wpdb->prefix . 'participants_database` 
    WHERE categorie = "Marchés"
  ';
  
  // now execute the query and get the results
  $raw_names = $wpdb->get_results( $query );
  
  /*
   * now expand the result array into an array for the options property of the 
   * dropdown
   */
  $options = array();
  foreach ( $raw_names as $record ) {
    $options[] = $record->titre;
  }
  
  // now set the field object with the new options list
  $field->options = $options;
  
  endif;
}