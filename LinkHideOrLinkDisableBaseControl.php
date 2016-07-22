<?

require_once(__DIR__ . "/AllBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

abstract class LinkHideOrLinkDisableBaseControl extends HideDeaktivLinkBaseControl
{

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("LinkSource", 0);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->RefreshLinks();
    }

    private function RefreshLinks()
    {
        if ($this->ReadPropertyInteger("LinkSource") == 0)
        {
            foreach (IPS_GetChildrenIDs($this->InstanceID) as $Child)
            {
                if (IPS_GetObject($Child)['ObjectType'] == 6)
                    IPS_DeleteLink($Child);
            }
            return;
        }
        $present = array();
        foreach (IPS_GetChildrenIDs($this->InstanceID) as $Child)
        {
            if (IPS_GetObject($Child)['ObjectType'] == 6)
                $present[] = IPS_GetLink($Child)['TargetID'];
        }

        $create = array_diff(IPS_GetChildrenIDs($this->ReadPropertyInteger("LinkSource")), $present);
        foreach ($create as $Target)
        {
            if (IPS_GetObject($Target)["ObjectIsHidden"])
                continue;
            $Link = IPS_CreateLink();
            IPS_SetParent($Link, $this->InstanceID);
            IPS_SetName($Link, IPS_GetName($Target));
            IPS_SetLinkTargetID($Link, $Target);
        }
    }

    protected function HideOrDeaktiv(bool $hidden)
    {
        if ($this->ReadPropertyBoolean("Invert"))
            $hidden = !$hidden;

        // Links erzeugen / prüfen wird nur bei ApplyChanges gemacht
        $Source = $this->ReadPropertyInteger("Source");
        $Childs = IPS_GetChildrenIDs($this->InstanceID);

        foreach ($Childs as $Child)
        {
            if (IPS_GetObject($Child)['ObjectType'] <> 6)
                continue;
            if (IPS_GetLink($Child)['TargetID'] == $Source)
                continue;
            $this->SetHiddenOrDisabled($Child, $hidden);
        }
    }

    abstract protected function SetHiddenOrDisabled($ObjectID, $Value);

}

?>