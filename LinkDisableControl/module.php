<?

require_once(__DIR__ . "/../LinkHideOrLinkDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class LinkDisableControl extends LinkHideOrLinkDisableBaseControl
{

    public function Create()
    {
        parent::Create();
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

    protected function SetHiddenOrDisabled($ObjectID, $Value)
    {
        if (IPS_GetObject($ObjectID)["ObjectIsDisabled"] <> $Value)
            IPS_SetDisabled($ObjectID, $Value);
    }

}

?>