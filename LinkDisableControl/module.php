<?

require_once(__DIR__ . "/../HideBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class LinkDisableControl extends DisableBaseControl
{

    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger("Target", 0);
        $this->RegisterPropertyInteger("TargetType", 1);
    }
    
    public function Destroy()
    {
        parent::Destroy();
        $this->UnRegisterEvent("UpdateLinkDisableControl");
    }
    
    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->RegisterEvent("UpdateLinkDisableControl", $this->ReadPropertyInteger("Source"), 'LINKDISABLE_Update($_IPS[\'TARGET\']);');
        $this->Update();
    }

    public function Update()
    {
        parent::Update();
    }

    protected function UnRegisterEvent($Name)
    {
        parent::UnRegisterEvent($Name);
    }

    protected function RegisterEvent($Name, $Source, $Script)
    {
        parent:: RegisterEvent($Name, $Source, $Script);
    }

}

?>