<?

require_once(__DIR__ . "/../LinkHideOrLinkDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class LinkDisableControl extends LinkHideOrLinkDisableBaseControl
{

    public function Destroy()
    {
        $this->UnRegisterEvent("UpdateLinkDisableControl");
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        try
        {
            $this->RegisterEvent("UpdateLinkDisableControl", $this->ReadPropertyInteger("Source"), 'LINKDISABLE_Update($_IPS[\'TARGET\']);');
        }
        catch (Exception $exc)
        {
            trigger_error($exc->getMessage(), $exc->getCode());
            return;
        }
        $this->Update();
    }

    public function Update()
    {
        parent::Update();
    }

    protected function SetHiddenOrDisabled($ObjectID, $Value)
    {
        if (IPS_GetObject($ObjectID)["ObjectIsDisabled"] <> $Value)
            IPS_SetDisabled($ObjectID, $Value);
    }

}

?>