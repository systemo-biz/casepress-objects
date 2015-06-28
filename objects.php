<?php
/*
Plugin Name: CasePress - Objects managment
Plugin URI: https://github.com/systemo-biz/casepress-objects
Description: Objects managment for CasePress based on WordPress
Author: CasePress
Author URI: http://casepress.org
GitHub Plugin URI: https://github.com/systemo-biz/casepress-objects
GitHub Branch: master
Version: 20150627-2
*/

include 'inc/model.php';
include 'inc/meta.php';
include 'inc/add_lists_objects.php';


//Временно отключил, пока не придумаю грамотную логику связи кейсов и объектов (так чтобы поле появлялось только у тех категорий, у которых нужно).
//include 'inc/objects_and_cases.php';