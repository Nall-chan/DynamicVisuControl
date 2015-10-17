<?

require_once(__DIR__ . "/../HideOrDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class HideControl extends HideOrDisableBaseControl
{

    public function Create()
    {
        parent::Create();
    }
    
    public function Destroy()
    {
        parent::Destroy();
        $this->UnRegisterEvent("UpdateHideControl");
    }
    
    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->RegisterEvent("UpdateHideControl", $this->ReadPropertyInteger("Source"), 'HIDE_Update($_IPS[\'TARGET\']);');
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
        if (IPS_GetObject($ObjectID)["ObjectIsHidden"] <> $Value)
            IPS_SetHidden($ObjectID, $Value);
    }
}

?>