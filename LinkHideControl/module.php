<?

require_once(__DIR__ . "/../LinkHideOrLinkDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class LinkHideControl extends LinkHideOrLinkDisableBaseControl
{

    public function Destroy()
    {
        $this->UnRegisterEvent("UpdateLinkHideControl");
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        try
        {
            $this->RegisterEvent("UpdateLinkHideControl", $this->ReadPropertyInteger("Source"), 'LINKHIDE_Update($_IPS[\'TARGET\']);');
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
        if (IPS_GetObject($ObjectID)["ObjectIsHidden"] <> $Value)
            IPS_SetHidden($ObjectID, $Value);
    }

}

?>