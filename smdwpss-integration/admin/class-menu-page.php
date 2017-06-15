<?php
/**
 * Creates the menu page for the plugin.
 *
 * @package WooCommerceSharpSpringIntegration
 */

/**
 * Creates the menu page for the plugin.
 *
 * Provides the functionality necessary for rendering the page corresponding
 * to the menu with which this page is associated.
 *
 * @package WooCommerceSharpSpringIntegration
 */
class Menu_Page {

	/**
	 * This function renders the contents of the page associated with the Submenu
	 * that invokes the render method. In the context of this plugin, this is the
	 * Submenu class.
	 */
	public function render() {
		// Set class property
        $this->options = get_option( 'wcssint-options' );
        ?>
        <div class="wrap">
            <h1>WCSSINT Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wcssint-general' );
                do_settings_sections( 'wcssint-admin-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
	}

	/**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'wcssint-general', // Option group
            'wcssint-options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'wcssint-main-section', 
            'My Custom Settings', 
            array( $this, 'print_section_info' ), // Callback
            'wcssint-admin-settings' // Page
        );  

        add_settings_field(
            'ecomm_enable', 
            'Enable E-Commerce', 
            array( $this, 'ecomm_enable_callback' ), // Callback
            'wcssint-admin-settings', // Page
            'wcssint-main-section' // Section           
        );

        add_settings_field(
            'account_number', 
            'Account Number',
            array( $this, 'account_number_callback' ), // Callback
            'wcssint-admin-settings', // Page
            'wcssint-main-section' // Section           
        );      

        add_settings_field(
            'domain_number', 
            'Domain Number', 
            array( $this, 'domain_number_callback' ), 
            'wcssint-admin-settings', 
            'wcssint-main-section'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['account_number'] ) )
            $new_input['account_number'] = sanitize_text_field( $input['account_number'] );

        if( isset( $input['domain_number'] ) )
            $new_input['domain_number'] = sanitize_text_field( $input['domain_number'] );

        if( isset( $input['ecomm_enable'] ) )
            $new_input['ecomm_enable'] = sanitize_text_field( $input['ecomm_enable'] );


        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function account_number_callback()
    {
        printf(
            '<input type="text" id="account_number" name="wcssint-options[account_number]" value="%s" />',
            isset( $this->options['account_number'] ) ? esc_attr( $this->options['account_number']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function domain_number_callback()
    {
        printf(
            '<input type="text" id="domain_number" name="wcssint-options[domain_number]" value="%s" />',
            isset( $this->options['domain_number'] ) ? esc_attr( $this->options['domain_number']) : ''
        );
    }

    public function ecomm_enable_callback()
    {
    	$wcssint_yesno = array(
    		'yes' 	=> "Yes",
    		'no'	=> "No"	
    		);

    	$selected_option = $this->options['ecomm_enable'];

    	?>
        <select name="wcssint-options[ecomm_enable]">
        	<?php foreach ($wcssint_yesno as $key => $value): ?>
        		<?php $adder = ( $selected_option == $key ) ? "selected" : ""; ?>
        		<option value="<?php echo $key; ?>" <?php echo $adder; ?>><?php echo $value; ?></option>
        	<?php endforeach; ?>
        </select>
        <?php
    }
}
