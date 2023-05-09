<?php
    require __DIR__ . '/../app/bootstrap.php';
    ini_set('memory_limit', -1);
    use \Magento\Framework\App\Bootstrap;
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $state = $objectManager->get('\Magento\Framework\App\State');
    $state->setAreaCode('frontend');
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    $action = $storeManager->getStore()->getBaseUrl().'develo_punchout/index/ocisetup';
?>
<p>Posting to: <?php echo $action; ?></p>
<form action="<?= $action;?>" method="POST" enctype="application/x-www-form-urlencoded">
    <div class="row">
        <div class="col" >
            <div class="form-group">
                <label for="username">Punchout Username</label>
                <input type="text" class="form-control" name="USERNAME" value="onlineportals+WE01401033@amticointernational.co.uk">
            </div>
            <div class="form-group">
                <label for="password">Punchout Password</label>
                <input type="text" class="form-control" name="PASSWORD" value="IgfFZr2BwPJjOfA2">
            </div>
            <div class="form-group">
                <label for="LANGUAGE">Language</label>
                <input type="text" class="form-control" name="LANGUAGE" value="EN">
            </div>
            <div class="form-group">
                <label for="HOOK_URL">Return URL</label>
                <input type="text" class="form-control" name="HOOK_URL" value="https://acme_buyer.sap.com/oci">
            </div>
            <div class="form-group">
                <label for="~TARGET">Target</label>
                <input type="text" class="form-control" name="~TARGET" value="_top">
            </div>
            <div class="form-group">
                <label for="~OkCode">OKCODE</label>
                <input type="text" class="form-control" name="~OkCode" value="ADDI">
            </div>
            <div class="form-group">
                <label for="~CALLER">CALLER</label>
                <input type="text" class="form-control" name="~CALLER" value="CTLG">
            </div>
            
            <div class="form-group">
                <label for="OCI_VERSION">Version</label>
                <input type="text" class="form-control" name="OCI_VERSION" value="4.0">
            </div>
        </div>
    
    
    </div>
    <div class="row">
        <div class="col-sm-2 offset-sm-10">
            <button class="btn btn-secondary btn-block" type="submit">Log in</button>
        </div>
    </div>
</form>
