<?php

//Связать с объектом
class CasesAndObjects_CP_Singleton {

private static $_instance = null;
    
    private function __construct() {
        add_action( 'add_meta_boxes', array( &$this, 'add' ) );
        add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
        add_filter('the_content', array( &$this, 'view' ));
        add_action( 'wp_ajax_case_objects', array($this, 'wp_ajax_case_objects_callback') );

    }
    
    //JSON for AJAX request
    function wp_ajax_case_objects_callback(){
        $query = new WP_Query( array(
                'fields' => 'ids',
                's' => $_GET['s'],
                'paged' => $_GET['paged'],
                'posts_per_page' => 10,
                'post_type' => array('objects')
            ));
        
        $elements = array();
        foreach ($query->posts as $post_id){

            $elements[] = array(
                    'id' => $post_id,
                    'title' => get_the_title($post_id),
                );
        }

        $data[] = array(
            "total" => (int)$query->found_posts, 
            "items" => $elements);

        wp_send_json($data[0]);
    }
    
    
    //init metabox
    function add() {
        add_meta_box('case_objects', __('Object', 'casepress'), array(&$this, 'add_meta_box_case_objects_callback'), 'cases', 'side');
    }

    //print HTML add contacts
    function add_meta_box_case_objects_callback($post){
        wp_nonce_field( basename( __FILE__ ), 'case_objects_nonce' );

        $case_objects = get_post_meta($post->ID, 'case_objects',true);

        ?>
            <div>
                <label for="case_objects">Выберите объект:</label><br/>
                <input type="none" name="case_objects" id="case_objects" class="field_cp" value="<?php echo $case_objects ?>" size="50">
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#case_objects").select2({
                            placeholder: "Выберите объект",
                            width: '100%',
                            allowClear: true,
                            minimumInputLength: 1,
                            ajax: {
                                    url: "<?php echo admin_url('admin-ajax.php?action=case_objects') ?>",
                                    dataType: 'json',
                                    quietMillis: 100,
                                    data: function (term, page) { // page is the one-based page number tracked by Select2
                                            return {
                                                paged: page, // page number
                                                s: term //search term
                                            };
                                    },
                                    results: function (data, page) {

                                            var more = (page * 10) < data.total; // whether or not there are more results available
                                            // notice we return the value of more so Select2 knows if more results can be loaded
                                            return {
                                                    results: data.items,
                                                    more: more
                                                };
                                    }
                            },

                            formatResult: function(element){ return "<div>" + element.title + "</div>" }, // omitted for brevity, see the source of this page
                            formatSelection: function(element){  return element.title; }, // omitted for brevity, see the source of this page
                            dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
                            escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
                        });
                        //Если есть данные о значении, то делаем выбор
                        <?php 
                            $case_objects_id = get_post_meta( $post->ID, 'case_objects', true );
                            if($case_objects_id != ''): ?>   
                            $("#case_objects").select2(
                                "data", 
                                <?php echo json_encode(array('id' => $case_objects_id, 'title' => get_the_title($case_objects_id))); ?>
                            ); 
                        <?php endif; ?>
                    });
                </script>     
            </div>
        <?php
    } 



    //save meta data
    function save($post_id) {

        // if autosave then cancel
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

        // check wpnonce
        if ( !isset( $_POST['case_objects_nonce'] ) || !wp_verify_nonce( $_POST['case_objects_nonce'], basename( __FILE__ ) ) ) return $post_id;

     
        //user can?
        if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;

        //go
        $post = get_post($post_id);
        
        update_post_meta($post_id, 'case_objects', esc_attr($_POST['case_objects']));
        
        return $post_id;
    }
    
    function view($content){
        $post = get_post();
        
        if('persons' != $post->post_type) return $content;
        
        $email = get_post_meta($post->ID, 'email',true);
        $tel = get_post_meta($post->ID, 'tel',true);
        $contacts_others = get_post_meta($post->ID, 'contacts_others',true);
        
        ob_start();
        ?>
        <div id="person_contacts" class="section_cp">
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
    
} $CasesAndObjects_CP = CasesAndObjects_CP_Singleton::getInstance();