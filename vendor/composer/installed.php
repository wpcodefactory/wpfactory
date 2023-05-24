<?php return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => NULL,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => NULL,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wpfactory/wpfactory-autoloader' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => 'd4387cb8fa8aa9f759dbc067ea9201d1d7a70cbc',
            'type' => 'library',
            'install_path' => __DIR__ . '/../wpfactory/wpfactory-autoloader',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
    ),
);
