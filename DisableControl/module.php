<?

require_once(__DIR__ . "/../HideOrDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class DisableControl extends HideOrDisableBaseControl
{

    public function Destroy()
    {
        $this->UnRegisterEvent("UpdateDisableControl");
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        try
        {
            $this->RegisterEvent("UpdateDisableControl", $this->ReadPropertyInteger("Source"), 'DISABLE_Update($_IPS[\'TARGET\']);');
        } catch (Exception $exc)
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