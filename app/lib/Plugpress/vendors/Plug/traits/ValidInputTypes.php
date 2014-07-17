<?php namespace Plug\traits;

trait ValidInputTypes {

    static protected $valid_types = array('text',
                                          'textarea',
                                          'select',
                                          'radio',
                                          'select-multiple',
                                          'checkbox',
                                          'url',
                                          'email',
                                          'tel',
                                          'search',
                                          'date',
                                          'month',
                                          'week',
                                          'time',
                                          'datetime-local',
                                          'number',
                                          'range',
                                          'color',
                                          'hidden',
                                          'password');
    
}

?>
