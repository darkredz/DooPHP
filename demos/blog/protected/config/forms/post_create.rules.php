<?php

//rules for create Post form
return array(
        'title' => array(
            array( 'maxlength', 145, 'Title cannot be longer than the 145 characters.' ),
            array( 'notnull' ),
            array( 'notEmpty', 'Title cannot be empty.' ),
        ),

        'content' => array(
            array( 'notnull' ),
            array( 'notEmpty', 'Post content cannot be empty!' ),
        ),

        'status' => array(
            array( 'integer', 'Invalid status for blog post' ),
            array( 'max', 1, 'Invalid status for blog post' ),
            array( 'min', 0, 'Invalid status for blog post' ),
        ),
        'tags'=>array(
            array('custom', 'AdminController::checkTags'),
            array('optional')
        )
    );
?>