<?

require_once(__DIR__ . "/AllBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

class HideOrDisableBaseControl extends HideDeaktivLinkBaseControl
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
    }
    
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
    }

    protected function HideOrDeaktiv(boolean $hidden)
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
        parent::RegisterEvent($Name, $Source, $Script);
    }

    protected function SetHiddenOrDisabled($ObjectID, $Value)
    {
    }

}

?>