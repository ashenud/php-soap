<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../Config/Authentication.php';
require_once __DIR__ . '/../../../Config/ProductMaster.php';

$server = new soap_server();
$namespace = 'http://cwm.salespad.lk/cwm-integration/api/soap/product_master/server.php?wsdl';
$server->configureWSDL('productMasterSyncService', $namespace);

// productMasterSync types
$server->wsdl->addComplexType('Product','complexType','struct','sequence','',
    array(
        'ProductCode' => array('name' => 'ProductCode','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'ProName' => array('name' => 'ProName','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'ProShortName' => array('name' => 'ProShortName','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'ProBrandCode' => array('name' => 'ProBrandCode','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'ProCategoryCode' => array('name' => 'ProCategoryCode','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'Minimum' => array('name' => 'Minimum','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'Maximum' => array('name' => 'Maximum','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'ProStatus' => array('name' => 'ProStatus','type' => 'xsd:boolean','minOccurs'=>'1','maxOccurs'=>'1'),
        'VatStatus' => array('name' => 'VatStatus','type' => 'xsd:boolean','minOccurs'=>'1','maxOccurs'=>'1'),
        'VatNo' => array('name' => 'VatNo','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'SalesOrganization' => array('name' => 'SalesOrganization','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'DistributionChannel' => array('name' => 'DistributionChannel','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'MaterialType' => array('name' => 'MaterialType','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'BaseUnit' => array('name' => 'BaseUnit','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'NetWeight' => array('name' => 'NetWeight','type' => 'xsd:decimal','minOccurs'=>'1','maxOccurs'=>'1'),
        'WeightUnit' => array('name' => 'WeightUnit','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'NetVolume' => array('name' => 'NetVolume','type' => 'xsd:decimal','minOccurs'=>'1','maxOccurs'=>'1'),
        'VolumeUnit' => array('name' => 'VolumeUnit','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'MaterialGroup1' => array('name' => 'MaterialGroup1','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'MaterialGroup2' => array('name' => 'MaterialGroup2','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'MaterialGroup3' => array('name' => 'MaterialGroup3','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'MaterialGroup4' => array('name' => 'MaterialGroup4','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'MaterialGroup5' => array('name' => 'MaterialGroup5','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
    )
);
$server->wsdl->addComplexType('ArrayOfProducts','complexType','struct','sequence','',
    array(
        'Products' => array('name' => 'Products','type' => 'tns:Product')
    )
);
$server->wsdl->addComplexType('ProductMasterDetail','complexType','struct','sequence','',
    array(
        'ProductMasters' => array('name' => 'ProductMasters','type' => 'tns:ArrayOfProducts'),
        '_token' => array('name' => '_token','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
    )
);

$server->wsdl->addComplexType('ProductMasterResponse','complexType','struct','sequence','',
    array(
        'Message' => array('name' => 'Message','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'InsertedCount' => array('name' => 'InsertedCount','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
        'DuplicateCount' => array('name' => 'DuplicateCount','type' => 'xsd:int','minOccurs'=>'1','maxOccurs'=>'1'),
    )
);

// authentication types
$server->wsdl->addComplexType('Credentials','complexType','struct','sequence','',
    array(
        'Username' => array('name' => 'Username','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        'Password' => array('name' => 'Password','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
    )
);
$server->wsdl->addComplexType('AuthenticationResponse','complexType','struct','sequence','',
    array(
        'Message' => array('name' => 'Message','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
        '_token' => array('name' => '_token','type' => 'xsd:string','minOccurs'=>'1','maxOccurs'=>'1'),
    )
);

//Register the web service operation
$server->register("productMasterSync",
	array('request' => 'tns:ProductMasterDetail'), //Request Object Type
    array('response' => 'tns:ProductMasterResponse') //Response Object Type
);

$server->register('authentication',
    array('request' => 'tns:Credentials'),
    array('response' => 'tns:AuthenticationResponse')
);

function productMasterSync($ProductMasterDetail){

    $token = $ProductMasterDetail['_token'];
    $product_list = $ProductMasterDetail['ArrayOfProducts']['Product'];

    $Authentication = new Authentication();
    $is_authenticated = $Authentication->check_authentication($token);

    if($is_authenticated) {
        $ProductMaster = new ProductMaster();
        $inserted_count = 0;
        $duplicate_count = 0;
        if($product_list['ProductCode'] == null) {
            foreach ($product_list as $key => $product) {
                if($ProductMaster->check_duplicate_product($product['ProductCode'])) {
                    $duplicate_count++;
                }
                else {
                    $ProductMaster->insert_product_master($product);
                    $inserted_count++;
                }
            }
        }
        else {
            if($ProductMaster->check_duplicate_product($product_list['ProductCode'])) {
                $duplicate_count++;
            }
            else {
                $ProductMaster->insert_product_master($product_list);
                $inserted_count++;
            }
        }
        
        $message = "Products has been synced successfully";
        $Response = array();
        $Response['Message'] = $message;
        $Response['InsertedCount'] = $inserted_count;
        $Response['DuplicateCount'] = $duplicate_count;
        // print_r(($ProductMaster));
        return $Response;
    }
    else {
        $message = "Authentication token has been expired";
        $Response = array();
        $Response['Message'] = $message;
        $Response['InsertedCount'] = 0;
        $Response['DuplicateCount'] = 0;
        // print_r(($token));
        return $Response;
    }
}

function authentication($Credentials){

    $username = $Credentials['Username'];
    $password = $Credentials['Password'];

    $Authentication = new Authentication();
    $authentication = $Authentication->user_authentication($username, $password);

    if($authentication) {
        $message = "Token has been generated successfully";
        $Response = array();
        $Response['Message'] = $message;
        $Response['_token'] = $authentication->token;
        print_r(($authentication));
        return $Response;
    }
    else {
        $message = "Authentication failed";
        $Response = array();
        $Response['Message'] = $message;
        $Response['_token'] = '';
        // print_r(($token));
        return $Response;
    }
}

if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA =file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA);


?>