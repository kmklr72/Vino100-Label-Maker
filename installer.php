<?php

function tb_install()
{
    add_option('tb_label_maker_config', array(
        'countries' => array(
            array(
                'slug'  => 'argentina',
                'name'  => 'Argentina',
            ),
            array(
                'slug'  => 'australia',
                'name'  => 'Australia',
            ),
            array(
                'slug'  => 'austria',
                'name'  => 'Austria',
            ),
            array(
                'slug'  => 'california',
                'name'  => 'California',
            ),
            array(
                'slug'  => 'canada',
                'name'  => 'Canada',
            ),
            array(
                'slug'  => 'chile',
                'name'  => 'Chile',
            ),
            array(
                'slug'  => 'france',
                'name'  => 'France',
            ),
            array(
                'slug'  => 'germany',
                'name'  => 'Germany',
            ),
            array(
                'slug'  => 'greece',
                'name'  => 'Greece',
            ),
            array(
                'slug'  => 'italy',
                'name'  => 'Italy',
            ),
            array(
                'slug'  => 'missouri',
                'name'  => 'Missouri',
            ),
            array(
                'slug'  => 'new_york',
                'name'  => 'New York',
            ),
            array(
                'slug'  => 'new_zealand',
                'name'  => 'New Zealand',
            ),
            array(
                'slug'  => 'oregon',
                'name'  => 'Oregon',
            ),
            array(
                'slug'  => 'portugal',
                'name'  => 'Portugal',
            ),
            array(
                'slug'  => 'South Africa',
                'name'  => 'south_africa',
            ),
            array(
                'slug'  => 'south_australia',
                'name'  => 'South Australia',
            ),
            array(
                'slug'  => 'spain',
                'name'  => 'Spain',
            ),
            array(
                'slug'  => 'switzerland',
                'name'  => 'Switzerland',
            ),
            array(
                'slug'  => 'usa',
                'name'  => 'USA',
            ),
            array(
                'slug'  => 'washington',
                'name'  => 'Washington',
            ),
        ),
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
    ), '', 'no');
}

function tb_uninstall()
{
    delete_option('tb_label_maker_config');
}

/* EOF */