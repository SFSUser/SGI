<?php

if (getEnv("MYSQL_USER") != "") {
    $container->setParameter('database_host', getEnv("MYSQL_SERVICE_HOST"));
    $container->setParameter('database_port', getEnv("MYSQL_SERVICE_PORT"));
    $container->setParameter('database_name',  getEnv("MYSQL_DATABASE"));
    //$container->setParameter('database_name',  "siim_ipscdo_com");
    $container->setParameter('database_user', getEnv("MYSQL_USER"));
    $container->setParameter('database_password', getEnv("MYSQL_PASSWORD"));
} /*else {
    $container->setParameter('database_host', "localhost");
    $container->setParameter('database_port', "3306");
    $container->setParameter('database_name', "ipscdo_sfs");
    $container->setParameter('database_user', "ipscdo_sfs");
    $container->setParameter('database_password', "SFS92111616401");
}*/
