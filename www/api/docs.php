<?php
require("../vendor/autoload.php");
use OpenApi\Serializer;
$openapi = \OpenApi\scan(__DIR__, array( "exclude" =>  ['docs', 'migrations', 'vendor']));
$s = $openapi->toJson();
$serializer = new Serializer();
$o = $serializer -> deserialize($s, "OpenApi\Annotations\OpenApi");
echo $o->toJson();

