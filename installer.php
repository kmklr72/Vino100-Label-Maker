<?php

function tb_install()
{
    add_option('tb_label_maker_config', array(
        'regions'   => array(
            array(
                'slug'  => 'united_states',
                'name'  => 'United States',
            ),
        ),
        'types'     => array(
            array(
                'slug'  => 'red',
                'name'  => 'Red',
            ),
            array(
                'slug'  => 'white',
                'name'  => 'White',
            ),
        ),
    ));
}

function tb_uninstall()
{
    delete_option('tb_label_maker_config');
}

/* EOF */