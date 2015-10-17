<?

require_once(__DIR__ . "/../HideOrDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class DisableControl extends HideOrDisableBaseControl
{

    public function Create()
    {
        parent::Create();
    }
    
    public function Destroy()
    {
        parent::Destroy();
        $this->UnRegisterEvent("UpdateDisableControl");
    }
    
    public function ApplyChanges()
    {
        parent::ApplyChanges();
        
        $this->RegisterEvent("UpdateDisableControl", $this->ReadPropertyInteger("Source"), 'DISABLE_Update($_IPS[\'TARGET\']);');
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
    
    protected function SetHiddenOrDisabled($ObjectID, $Value)
    {
        if (IPS_GetObject($ObjectID)["ObjectIsDisabled"] <> $Value)
            IPS_SetDisabled ($ObjectID, $Value);
    }
}

?>