<?php
    
    namespace Develodesign\Punchout\Controller\Adminhtml\PunchoutGroup;

    use Magento\Framework\App\Response\Http\FileFactory;
    use Magento\Framework\Exception\FileSystemException;
    use Magento\Framework\Filesystem\DirectoryList;
    use Magento\Framework\Registry;
    use Magento\Backend\App\Action\Context;
    use Magento\Framework\Filesystem\DriverInterface;

class DownloadFile extends \Develodesign\Punchout\Controller\Adminhtml\PunchoutGroup
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $downloader;
    
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directory;
    
    /**
     * @var DriverInterface
     */
    protected $driverInterface;
    
    /**
     * @param Context       $context
     * @param Registry      $coreRegistry
     * @param FileFactory   $fileFactory
     * @param DirectoryList $directory
     */
    public function __construct(
        Context $context,
        Registry  $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $directory,
        DriverInterface $driverInterface
    ) {
        
        $this->downloader = $fileFactory;
        $this->directory = $directory;
        $this->driverInterface = $driverInterface;

        parent::__construct($context, $coreRegistry);
    }
    
    /**
     * @throws FileSystemException
     * @throws \Exception
     */
    public function execute()
    {
        $filepath = "customer";
        $filename = "punchoutgroupsample.csv";
        $file = $this->directory->getPath('media') . '/' . $filepath;
        return $this->downloader->create(
            $filename,
            $this->driverInterface->fileGetContents($file)
        );
    }
}
