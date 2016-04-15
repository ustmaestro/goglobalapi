<?php
require_once( path_to_loader_class . '/Loader.php');

use ustmaestro\goglobalapi\GGService;
use ustmaestro\goglobalapi\lib\GGSearchModel;

$config['agency'] = 'Your GG Agency ID';
$config['user'] = 'Your GG Username';
$config['password'] = 'Your GG Password';

$service = new GGService($config);
$model = new GGSearchModel();

$model->city_code = 2016;       // GG internal city code
$model->check_in = '20161003';  // check in date
$model->nights = 3;             // number of nights

$model->rooms_number = 2;       // number of rooms

$model->rooms_persons = [       // persons count by rooms !!! attention index starts with -1-
    '1' => [                    // room 1
        'adult' => 1,           // adults count
        'child' => 0,           // childrens count
    ],
    '2' => [                    // room 1
        'adult' => 2,           // adults count
        'child' => 1,           // childrens count
    ],
];

$results =  $service->searchHotels($model);
var_dump($results);
