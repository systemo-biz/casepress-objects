<?php

class ObjectMeta_CP_Singleton {

private static $_instance = null;
    
private function __construct() {
    add_action( 'add_meta_boxes', array( &$this, 'add' ) );
    add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
    add_action( 'wp_ajax_object_responsible', array($this, 'object_responsible_callback') );
    add_action( 'wp_ajax_object_owner', array($this, 'object_owner_callback') );
    add_filter('the_content', array( &$this, 'view' ));

    

}

    
    //Query object owner
    function object_owner_callback(){
        $query = new WP_Query( array(
                                                            'fields' => 'ids',
                                                            's' => $_GET['s'],
                                                            'paged' => $_GET['paged'],
                                                            'posts_per_page' => 10,
                                                            'post_type' => array('persons', 'organizations')
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
                'items' => $elements
            );

            wp_send_json($data[0]);
    }
    
    //Query object_responsible
    function object_responsible_callback(){
        
        $query = new WP_Query( array(
                                                        'fields' => 'ids',
                                                        's' => $_GET['s'],
                                                        'paged' => $_GET['paged'],
                                                        'posts_per_page' => 10,
                                                        'post_type' => array('persons')
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
    add_meta_box('object_data', __('Data for object', 'casepress'), array(&$this, 'object_data_callback'), 'objects', 'normal');

}

    //print HTML 
    function object_data_callback($post){
        wp_nonce_field( basename( __FILE__ ), 'object_data_nonce' );

        $object_owner = get_post_meta($post->ID, 'object_owner',true);
        $object_responsible = get_post_meta($post->ID, 'object_responsible',true);

        ?>
            <p id="object_owner_wrapper">
                <label for="object_owner">Владелец</label><br/>
                <input type="text" name="object_owner" id="object_owner" class="field_cp" value="<?php echo $tel ?>" size="50">
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#object_owner").select2({
                            placeholder: "Выберите владельца",
                            width: '100%',
                            allowClear: true,
                            minimumInputLength: 1,
                            ajax: {
                                    url: "<?php echo admin_url('admin-ajax.php?action=object_owner') ?>",
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
                            $object_owner_id = get_post_meta( $post->ID, 'object_owner', true );
                            if($object_owner_id != ''): ?>   
                            $("#object_owner").select2(
                                "data", 
                                <?php echo json_encode(array('id' => $object_owner_id, 'title' => get_the_title($object_owner_id))); ?>
                            ); 
                        <?php endif; ?>
                    });
                </script>
            </p>
            <p id="object_responsible_wrapper">
                <label for="object_responsible">Ответственный:</label><br/>
                <input type="text" name="object_responsible" id="object_responsible" class="field_cp" value="<?php echo $email ?>" size="50">
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#object_responsible").select2({
                            placeholder: "Выберите ответственного",
                            width: '100%',
                            allowClear: true,
                            minimumInputLength: 1,
                            ajax: {
                                    url: "<?php echo admin_url('admin-ajax.php?action=object_responsible') ?>",
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
                            $responsible_id = get_post_meta( $post->ID, 'object_responsible', true );
                            if($responsible_id != ''): ?>   
                            $("#object_responsible").select2(
                                "data", 
                                <?php echo json_encode(array('id' => $responsible_id, 'title' => get_the_title($responsible_id))); ?>
                            ); 
                        <?php endif; ?>
                    });
                </script>                  
            </p> <!-- #object_responsible_wrapper -->
        <?php
    } 



    //save meta data
    function save($post_id) {

        // if autosave then cancel
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

        // check wpnonce
        if ( !isset( $_POST['object_data_nonce'] ) || !wp_verify_nonce( $_POST['object_data_nonce'], basename( __FILE__ ) ) ) return $post_id;

        //user can?
        if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;

        //go
        $post = get_post($post_id);
        update_post_meta($post_id, 'object_owner', esc_attr($_POST['object_owner']));
        update_post_meta($post_id, 'object_responsible', esc_attr($_POST['object_responsible']));

        return $post_id;
    }
    
    function view($content){
        $post = get_post();
        
        if(!(is_singular( 'objects' ))) return $content;
        
        $object_owner = get_post_meta($post->ID, 'object_owner',true);
        $object_responsible = get_post_meta($post->ID, 'object_responsible',true);
        
        ob_start();
        ?>
        <div id="object_data" class="section_cp">
            <ul>
                <li>
                    <span>Владелец: </span>
                    <a href="<?php echo get_permalink ($object_owner); ?>"><?php echo get_the_title($object_owner); ?></a>
                </li>
                <li>
                    <span>Ответственный: </span>
                    <a href="<?php echo get_permalink ($object_responsible); ?>"><?php echo get_the_title($object_responsible); ?></a>
                </li>
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