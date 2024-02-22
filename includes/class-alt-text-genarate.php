<?php
class Alt_Text_Generate {
    // private $image_url = ''; // Declare the private property to store image URL

    public function __construct() {
        // Hook to display data for all media images
        add_action('admin_notices', array($this, 'display_all_media_images_data'));

        add_action('add_attachment', array($this, 'update_alt_text_on_upload'));

        // Hook to add custom fields to media attachment details
        add_filter('attachment_fields_to_edit', array($this, 'add_custom_attachment_field'), 10, 2);

        // Enqueue the JavaScript file in the WordPress admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_scripts'));
    }

    /**
     * Enqueue custom JavaScript file.
     */
    public function enqueue_custom_scripts() {
        // Enqueue your custom JavaScript file
        wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'custom.js', array(), '1.0', true);
    }

    /**
     * Add custom field to media attachment details.
     *
     * @param array $form_fields Default form fields.
     * @param object $post Media attachment post object.
     * @return array Custom form fields.
     */
    public function add_custom_attachment_field($form_fields, $post) {
        // Add a button next to the text field
        $this->image_url = wp_get_attachment_image_src($post->ID, 'full')[0]; // Store image URL
        $form_fields['serial_number_button'] = array(
            'input' => 'html', // Use 'html' input type for a button
            'html'  => '<button class="custom-generate-ai-button" onclick="generateAIAlternativeText(\'' . esc_js($this->image_url) . '\')"><span class="dashicons dashicons-cover-image"></span>Generate Alternative Text With AI</button>',
        );
    
        return $form_fields;
    }

    /**
     * Custom function to remove special characters and replace spaces with an underscore.
     *
     * @param string $str Input string.
     * @return string Sanitized string.
     */
    private function sanitize_title_custom($str) {
        $str = preg_replace('/[^\w\s]/', ' ', $str); // Remove special characters
        $str = ltrim($str); // Remove leading spaces
        return $str;
    }

    /**
     * Display data for all media images.
     */
    public function display_all_media_images_data() {
        global $wpdb;

        $query = "SELECT * FROM $wpdb->posts WHERE post_type = 'attachment'";

        $media_data = $wpdb->get_results($query, ARRAY_A);

        foreach ($media_data as $media) {
            $image_id = $media['ID'];
            $image_title = $media['post_title'];
            $alt_text = $this->sanitize_title_custom($image_title);
            $caption = $media['post_excerpt'];
            $description = $media['post_content'];
        }
    }

     /**
     * Update alt text when an image is uploaded.
     *
     * @param int $attachment_id Attachment ID.
     */
    public function update_alt_text_on_upload($attachment_id) {
        $image_title = get_the_title($attachment_id);

        $alt_text = $this->sanitize_title_custom($image_title);

        // Get image URL using wp_get_attachment_image_src
        // $image_url = wp_get_attachment_image_src($attachment_id, 'full')[0];

        // Set _wp_attachment_image_alt to the image URL
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
    }
}

// Instantiate the class
$alt_text_generate = new Alt_Text_Generate();
?>
