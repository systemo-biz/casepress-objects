<?php

class ObjectMeta_CP_Singleton {

private static $_instance = null;
    
private function __construct() {
    add_action( 'add_meta_boxes', array( &$this, 'add' ) );
    add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
    add_filter('the_content', array( &$this, 'view' ));
}
    
//init metabox
function add() {
    add_meta_box('object_data', __('Data for object', 'casepress'), array(&$this, 'object_data_callback'), 'objects', 'normal');

}

    //print HTML 
    function object_data_callback($post){
        wp_nonce_field( basename( __FILE__ ), 'object_data_nonce' );

        $email = get_post_meta($post->ID, 'email',true);
        $tel = get_post_meta($post->ID, 'tel',true);
        $contacts_others = get_post_meta($post->ID, 'contacts_others',true);

        ?>
            <p>
                <label for="person_phone">Телефон (основной):</label><br/>
                <small>Номер телефона, по которому проще всего связаться с персоной</small><br/>
                <input type="text" name="tel" id="person_phone" class="field_cp" value="<?php echo $tel ?>" size="50">
            </p>
            <p>
                <label for="person_email">Email (основной):</label><br/>
                <input type="text" name="email" id="person_email" class="field_cp" value="<?php echo $email ?>" size="50">
            </p>
            <p>
                <label for="person_contacts_others">Прочие контактные данные:</label><br/>
                <textarea rows="3" cols="70" name="contacts_others" id="person_contacts_others" class="field_cp"><?php echo $contacts_others ?></textarea>
            </p>
        <?php
    } 



    //save meta data
    function save($post_id) {

        // check wpnonce
        if ( !isset( $_POST['object_data_nonce'] ) || !wp_verify_nonce( $_POST['object_data_nonce'], basename( __FILE__ ) ) ) return $post_id;

        // if autosave then cancel
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

        //user can?
        if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;

        //go
        $post = get_post($post_id);
        update_post_meta($post_id, 'email', esc_attr($_POST['email']));
        update_post_meta($post_id, 'tel', esc_attr($_POST['tel']));
        update_post_meta($post_id, 'contacts_others', esc_attr($_POST['contacts_others']));

        return $post_id;
    }
    
    function view($content){
        $post = get_post();
        
        if(!(is_singular( 'objects' ))) return $content;
        
        $email = get_post_meta($post->ID, 'email',true);
        $tel = get_post_meta($post->ID, 'tel',true);
        $contacts_others = get_post_meta($post->ID, 'contacts_others',true);
        
        ob_start();
        ?>
        <div id="object_data" class="section_cp">
            <ul>
                <li>Телефон: <?php echo $tel; ?></li>
                <li>Email: <?php echo $email; ?></li>
                <?php
                    if($contacts_others) echo "<li>Прочие контакты:<br/>" . $contacts_others . "</li>";
                ?>
            </ul>
        </div>
        <?php
        $html = ob_get_contents();
        ob_get_clean();
        return $html . $content;
    }
     
/**
 * Служебные функции одиночки
 */
protected function __clone() {
	// ограничивает клонирование объекта
}
static public function getInstance() {
	if(is_null(self::$_instance))
	{
	self::$_instance = new self();
	}
	return self::$_instance;
}    
    
} $ObjectMeta_CP = ObjectMeta_CP_Singleton::getInstance();