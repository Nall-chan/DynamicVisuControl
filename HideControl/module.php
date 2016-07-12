<?

require_once(__DIR__ . "/../HideOrDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class HideControl extends HideOrDisableBaseControl
{

    public function Destroy()
    {
        $this->UnRegisterEvent("UpdateHideControl");
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        try
        {
            $this->RegisterEvent("UpdateHideControl", $this->ReadPropertyInteger("Source"), 'HIDE_Update($_IPS[\'TARGET\']);');
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