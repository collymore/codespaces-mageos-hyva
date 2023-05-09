<?php
    require __DIR__ . '/../app/bootstrap.php';
    ini_set('memory_limit', -1);
    use \Magento\Framework\App\Bootstrap;
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $state = $objectManager->get('\Magento\Framework\App\State');
    $state->setAreaCode('frontend');
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');    
    $serverLocal = $storeManager->getStore()->getBaseUrl().'develo_punchout/purchaseorder';
        
    $cxml = file_get_contents("po.xml");

    try {
        $ch = curl_init();
        if (FALSE === $ch)
            throw new Exception('failed to initialize curl is it installed?');
        curl_setopt( $ch, CURLOPT_URL, $serverLocal );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 100 );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $cxml );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, ["Content-type: text/xml","Content-length: " . strlen( $cxml ),"Connection: close"] );
        $data = curl_exec( $ch );

        if (FALSE === $data)
            throw new Exception(curl_error($ch), curl_errno($ch));
        
    } catch(Exception $e) {
        var_dump($e->getMessage());
        die;
    }
    
    echo "<br/></br><h1>Order Request URL:</h1> " . $serverLocal. "";
    echo "<br/></br><h1>Response:</h1> <pre style=\"white-space: pre-wrap\">";
    print_r(htmlspecialchars($data));
    echo "</pre><br/></br><br/><h1>Input Xml :</h1><pre> " . htmlspecialchars($cxml);