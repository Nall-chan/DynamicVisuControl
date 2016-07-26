<?

require_once(__DIR__ . "/AllBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

abstract class HideOrDisableBaseControl extends HideDeaktivLinkBaseControl
{

    public function Create()
    {
        parent::Create();
        
        $this->RegisterPropertyInteger("Target", 0);
        $this->RegisterPropertyInteger("TargetType", 1);        
    }

    
    protected function HideOrDeaktiv(bool $hidden)
    {
        if ($this->ReadPropertyBoolean("Invert"))
            $hidden = !$hidden;

        if ($this->ReadPropertyInteger("Target") == 0)
        {
            echo "Target invalid.";
            return;
        }

        $Target = $this->ReadPropertyInteger("Target");

        if (!IPS_ObjectExists($Target))
        {
            echo "Target invalid.";
            return;
        }

        if ($this->ReadPropertyInteger("TargetType") == 0)
        {
            $this->SetHiddenOrDisabled($Target, $hidden);
        }
        elseif ($this->ReadPropertyInteger("TargetType") == 1)
        {
            $Source = $this->ReadPropertyInteger("Source");
            $Childs = IPS_GetChildrenIDs($Target);
            foreach ($Childs as $Child)
            {
                if ($Child == $Source)
                    continue;
                if (IPS_GetObject($Child)['ObjectType'] == 6)
                    if (IPS_GetLink($Child)['TargetID'] == $Source)
                        continue;
                $this->SetHiddenOrDisabled($Child, $hidden);
            }
        }
        else
        {
            echo "TargetType invalid.";
            return;
        }
    }

    abstract protected function SetHiddenOrDisabled($ObjectID, $Value);

}

?>
