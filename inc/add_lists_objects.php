<?php

//Добавляем список объектов на страницу владельцев
function add_list_objects_to_owners($content){
    
    if(! is_singular(array('persons', 'organizations'))) return $content;
    
    $post = get_post();

    $items = get_posts('post_type=objects&meta_key=object_owner&meta_value='.$post->ID);
    
    if(empty($items)) return $content;
    
    ob_start();
    ?>
        <section id="list_objects_for_owner" class="section_cp">
            <header>
	    	  <h1>Владеет объектами</h1>
	    	  <hr>
            </header>
            <ul>
                <?php
                    foreach($items as $item) {
                        ?>
                            <li>
                                <a href="<?php echo get_permalink ($item->ID); ?>"><?php echo get_the_title($item->ID); ?></a>
                            </li>
                        <?php
                    }
                ?>
            </ul>
            <a href="<?php echo add_query_arg( array('post_type'=>'objects','meta_object_owner'=>$post->ID), get_site_url()); ?>" class='btn btn-default'>Все записи</a>

        </section>
    <?php
    $html = ob_get_contents();
    ob_get_clean();
    return $content . $html;
} add_filter('the_content', 'add_list_objects_to_owners');


//Добавляем список объектов на страницу ответственного
function add_list_objects_to_responsible($content){
    
    if(! is_singular('persons')) return $content;
    
    $post = get_post();

    $items = get_posts('post_type=objects&meta_key=object_responsible&meta_value='.$post->ID);
    
    if(empty($items)) return $content;
    
    ob_start();
    ?>
        <section id="list_objects_for_responsible" class="section_cp">
            <header>
	    	  <h1>Отвечает за объекты</h1>
	    	  <hr>
            </header>
            <ul>
                <?php
                    foreach($items as $item) {
                        ?>
                            <li>
                                <a href="<?php echo get_permalink ($item->ID); ?>"><?php echo get_the_title($item->ID); ?></a>
                            </li>
                        <?php
                    }
                ?>
            </ul>
            <a href="<?php echo add_query_arg( array('post_type'=>'objects','meta_object_responsible'=>$post->ID), get_site_url()); ?>" class='btn btn-default'>Все записи</a>

        </section>
    <?php
    $html = ob_get_contents();
    ob_get_clean();
    return $content . $html;
} add_filter('the_content', 'add_list_objects_to_responsible');
