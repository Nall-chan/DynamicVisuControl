[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FDynamicVisuControl%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)
]()
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FDynamicVisuControl%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)
](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v80-v81-q3-2025/)  
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Check Style](https://github.com/Nall-chan/DynamicVisuControl/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/DynamicVisuControl/actions) [![Run Tests](https://github.com/Nall-chan/DynamicVisuControl/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/DynamicVisuControl/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#4-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#4-spenden)  
# Dynamic Visu Control <!-- omit in toc -->
Enthält verschiedene Module für die dynamische Visualisierung von Objekten im WebFront von IP-Symcon.

## Dokumentation <!-- omit in toc -->

**Inhaltsverzeichnis**

- [1. Vorbemerkungen](#1-vorbemerkungen)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Software-Installation](#3-software-installation)
- [4. Enthaltene Module](#4-enthaltene-module)
  - [HideControl](#hidecontrol)
  - [DisableControl](#disablecontrol)
  - [LinkHideControl](#linkhidecontrol)
  - [LinkDisableControl](#linkdisablecontrol)
- [5. Anhang](#5-anhang)
  - [1. GUID der Module](#1-guid-der-module)
  - [2. Eigenschaften der Instanzen](#2-eigenschaften-der-instanzen)
  - [3. Changelog](#3-changelog)
  - [4. Spenden](#4-spenden)
- [6. Lizenz](#6-lizenz)

## 1. Vorbemerkungen
 **Die Visualisierung im WebFront von IPS sollte nicht direkt mit den Original-Hardware-Instanzen erfolgen.  
 Es empfiehlt sich eine eigene Struktur aus Kategorien, Instanzen des Typ Dummy-Modul und Links zu erzeugen.  
 Da die Eigenschaften 'Sichtbarkeit' und 'Bedienbarkeit' von Links nicht von ihrem Ziel vererbt werden, ist es nicht sinnvoll direkt Hardware-Instanzen zu verstecken oder zu deaktivieren.**  
 
## 2. Voraussetzungen

* IP-Symcon ab Version 8.1

## 3. Software-Installation
  
  Über den 'Module-Store' in IPS das Modul 'Dynamic Visu Control' hinzufügen.  
   **Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.**  
![Module-Store](imgs/install.png) 

## 4. Enthaltene Module

### HideControl
 Versteckt/visualisiert ein vorhandenes Objekt oder dessen direkten Unterobjekte in Abhängigkeit einer Variable.  
 Dazu wird die Variable <span style="color:red">__(1)__</span> mit den jeweiligen Wert aus <span style="color:red">__(2)__</span> oder <span style="color:red">__(5)__</span> verglichen.  
 Ist der Vergleich erfolgreich (= wahr) so wird das Ziel-Objekt <span style="color:red">__(3)__</span> versteckt.  
 Optional kann der Parameter Invertieren genutzt werden um den Vergleich umzudrehen.  
 
![Doku/HideControl_1.png](imgs//HideControl_1.png)  
![Doku/HideControl_2.png](imgs//HideControl_2.png)  

 <span style="color:red">__1.__</span> Die Variable welche zum Vergleich herangezogen wird.  
 <span style="color:red">__2.__</span> Sollte es sich bei <span style="color:red">__(1)__</span> um eine Variable vom Typ `boolean` handeln, so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__3.__</span> Das Ziel welches versteckt werden soll.  
 <span style="color:red">__4__</span> Hier kann festgelegt werden, ob nur das Ziel <span style="color:red">__(3)__</span>, oder dessen Unterobjekte versteckt werden sollen.  
 <span style="color:red">__5.__</span> Ist die Variable unter <span style="color:red">__(1)__</span> nicht vom Typ `boolean` so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__6.__</span> Zeigt alle, beim Zustand aus, versteckten Unterobjekte.  
     Es ist auch zu sehen, dass das erste Objekt nicht versteckt wurde, da es sich um einen Link zur Variable <span style="color:red">__(1)__</span> handelt.  
  
**Achtung:**  
  Befindet sich die Variable <span style="color:red">__(1)__</span> auch unterhalb dem zu versteckenden Objekt, so ist diese im WebFront dann auch nicht mehr sichtbar.  
  Dies kann gewollt, aber auch hinderlich sein.  
  Darum kann alternativ unter <span style="color:red">__(4)__</span> festgelegt werden, das nur Unterobjekte versteckt werden.  
  Bei dieser Einstellung wird beim verstecken geprüft, ob unter den Unterobjekten auch die Variable (oder ein Link zur Variable) enthalten ist.  
  Dieses Objekt wird dann **nicht** versteckt.  
  
---  
  
### DisableControl
 Deaktiviert/aktiviert ein vorhandenes Objekt oder dessen direkten Unterobjekte in Abhängigkeit einer Variable.  
 Dazu wird die Variable <span style="color:red">__(1)__</span> mit den jeweiligen Wert aus <span style="color:red">__(2)__</span> oder <span style="color:red">__(5)__</span> verglichen.  
 Ist der Vergleich erfolgreich (= wahr) so wird das Ziel-Objekt <span style="color:red">__(3)__</span> deaktiviert.  
 Optional kann der Parameter Invertieren genutzt werden um den Vergleich umzudrehen.  
 
![Doku/DisableControl_2.png](imgs//DisableControl_2.png)  

 <span style="color:red">__1.__</span> Die Variable welche zum Vergleich herangezogen wird.  
 <span style="color:red">__2.__</span> Sollte es sich bei <span style="color:red">__(1)__</span> um eine Variable vom Typ `boolean` handeln, so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__3.__</span> Das Ziel welches deaktiviert werden soll.  
 <span style="color:red">__4.__</span> Hier kann festgelegt werden, ob nur das Ziel <span style="color:red">__(3)__</span>, oder dessen Unterobjekte deaktiviert werden sollen.  
 <span style="color:red">__5.__</span> Ist die Variable unter <span style="color:red">__(1)__</span> nicht vom Typ `boolean` so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__6.__</span> Zeigt alle, beim Zustand aus, deaktivierten Unterobjekte im Objektbaum.  
 <span style="color:red">__7.__</span> Darstellung der deaktivierten Unterobjekte im WebFront.  
     Es ist auch zu sehen, dass das erste Objekt nicht deaktiviert wurde, da es sich um einen Link zur Variable <span style="color:red">__(1)__</span> handelt.  

**Achtung:**  
  Befindet sich die Variable <span style="color:red">__(1)__</span> auch unterhalb dem zu deaktivierenden Objekt, so ist diese im WebFront dann auch nicht mehr bedienbar.  
  Dies kann gewollt, aber auch hinderlich sein.  
  Darum kann alternativ unter <span style="color:red">__(4)__</span> festgelegt werden, das nur Unterobjekte deaktiviert werden.  
  Bei dieser Einstellung wird beim deaktivieren geprüft, ob unter den Unterobjekten auch die Variable (oder ein Link zur Variable) enthalten ist.  
  Dieses Objekt wird dann **nicht** deaktiviert.  

---

### LinkHideControl
 Erzeugt Links zu Unterobjekte eines ausgewählten Objektes und versteckt/visualisiert diese Links in Abhängigkeit einer Variable.  
 Die Links werden aus allen direkten Unterobjekten des Quell-Objektes <span style="color:red">__(3)__</span> automatisch erzeugt.  
 Dabei werden versteckte Objekte im Quell-Objekt <span style="color:red">__(3)__</span> ignoriert.  
 Zum Vergleich wird wieder die Variable <span style="color:red">__(1)__</span> mit den jeweiligen Wert aus <span style="color:red">__(2)__</span> oder <span style="color:red">__(5)__</span> verglichen.  
 Ist der Vergleich erfolgreich (= wahr) so werden die vorher erzeugten Links versteckt.  
 Optional kann der Parameter Invertieren genutzt werden um den Vergleich umzudrehen.  

![Doku/LinkHideControl_1](imgs//LinkHideControl_1.png)  
![Doku/LinkHideControl_2](imgs//LinkHideControl_2.png)  

 <span style="color:red">__1.__</span> Die Variable welche zum Vergleich herangezogen wird.  
 <span style="color:red">__2.__</span> Sollte es sich bei <span style="color:red">__(1)__</span> um eine Variable vom Typ `boolean` handeln, so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__3.__</span> Das Quell-Objekt von dessen Unterobjekte Links erzeugt werden sollen.  
 <span style="color:red">__4.__</span>  -entfällt-  
 <span style="color:red">__5.__</span> Ist die Variable unter <span style="color:red">__(1)__</span> nicht vom Typ `boolean` so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__6.__</span> Zeigt die automatisch erstellen Links.  
  
**Achtung:**  
  Diese Instanz ist für die direkte Visualisierung gedacht.  

---

### LinkDisableControl
 Erzeugt Links zu Unterobjekte eines ausgewählten Objektes und deaktiviert/aktiviert diese Links in Abhängigkeit einer Variable.  
 Die Links werden aus allen direkten Unterobjekten des Quell-Objektes <span style="color:red">__(3)__</span> automatisch erzeugt.  
 Dabei werden versteckte Objekte im Quell-Objekt <span style="color:red">__(3)__</span> ignoriert.  
 Zum Vergleich wird wieder die Variable <span style="color:red">__(1)__</span> mit den jeweiligen Wert aus <span style="color:red">__(2)__</span> oder <span style="color:red">__(5)__</span> verglichen.  
 Ist der Vergleich erfolgreich (= wahr) so werden die vorher erzeugten Links versteckt.  
 Optional kann der Parameter Invertieren genutzt werden um den Vergleich umzudrehen.  
 
![Doku/LinkDisableControl_2.png](imgs//LinkDisableControl_2.png)  

 <span style="color:red">__1.__</span> Die Variable welche zum Vergleich herangezogen wird.  
 <span style="color:red">__2.__</span> Sollte es sich bei <span style="color:red">__(1)__</span> um eine Variable vom Typ `boolean` handeln, so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__3.__</span> Das Quell-Objekt von dessen Unterobjekte Links erzeugt werden sollen.  
 <span style="color:red">__4.__</span> -entfällt-  
 <span style="color:red">__5.__</span> Ist die Variable unter <span style="color:red">__(1)__</span> nicht vom Typ `boolean` so ist hier der Vergleichswert einzutragen.  
 <span style="color:red">__6.__</span> Zeigt die automatisch erstellen Links.  
  
**Achtung:**  
  Diese Instanz ist für die direkte Visualisierung gedacht.  


## 5. Anhang

###  1. GUID der Module

|      Instanz       |                  GUID                  |
| :----------------: | :------------------------------------: |
|    HideControl     | {A9347205-0889-4D01-BDD2-C377FC0E39D9} |
|   DisableControl   | {61618A2B-D39D-4F1D-B27E-DEF2CF9452F9} |
|  LinkHideControl   | {37BC47EE-E95A-4DAF-A408-129D778F7AB5} |
| LinkDisableControl | {E94821F4-1647-440B-BB2A-76F8CF1CBB16} |


###  2. Eigenschaften der Instanzen

**Eigenschaften von HideControl:**  

| Eigenschaft |   Typ   | Standardwert |                             Funktion                              |
| :---------: | :-----: | :----------: | :---------------------------------------------------------------: |
|   Source    | integer |      1       |             Quell-Variable welche zum Vergleich dient             |
|    Value    | string  |      []      |               Vergleichswert für Wert, JSON kodiert               |
|   Invert    | boolean |    false     |    True wenn der Vergleich noch einmal invertiert werden soll     |
|   Target    | integer |      1       |   IPS-Objekt-ID des Ziel-Objektes welche versteckt werden soll    |
| TargetType  | integer |      0       | 0 Wenn Target, 1 wenn dessen Unterobjekte versteckt werden sollen |

**Eigenschaften von DisableControl:**  

| Eigenschaft |   Typ   | Standardwert |                              Funktion                               |
| :---------: | :-----: | :----------: | :-----------------------------------------------------------------: |
|   Source    | integer |      1       |              Quell-Variable welche zum Vergleich dient              |
|    Value    | string  |      []      |                Vergleichswert für Wert, JSON kodiert                |
|   Invert    | boolean |    false     |     True wenn der Vergleich noch einmal invertiert werden soll      |
|   Target    | integer |      1       |   IPS-Objekt-ID des Ziel-Objektes welche deaktiviert werden soll    |
| TargetType  | integer |      0       | 0 Wenn Target, 1 wenn dessen Unterobjekte deaktiviert werden sollen |

**Eigenschaften von LinkHideControl:**  

| Eigenschaft |   Typ   | Standardwert |                          Funktion                          |
| :---------: | :-----: | :----------: | :--------------------------------------------------------: |
|   Source    | integer |      1       |         Quell-Variable welche zum Vergleich dient          |
|    Value    | string  |      []      |           Vergleichswert für Wert, JSON kodiert            |
|   Invert    | boolean |    false     | True wenn der Vergleich noch einmal invertiert werden soll |
| LinkSource  | integer |      1       |         IPS-Objekt-ID welches verlinkt werden soll         |

**Eigenschaften von LinkDisableControl:**  

| Eigenschaft |   Typ   | Standardwert |                          Funktion                          |
| :---------: | :-----: | :----------: | :--------------------------------------------------------: |
|   Source    | integer |      1       |         Quell-Variable welche zum Vergleich dient          |
|    Value    | string  |      []      |           Vergleichswert für Wert, JSON kodiert            |
|   Invert    | boolean |    false     | True wenn der Vergleich noch einmal invertiert werden soll |
| LinkSource  | integer |      1       |         IPS-Objekt-ID welches verlinkt werden soll         |

### 3. Changelog

**Version 3.70:**  
 - Release für Symcon 8.1  
 - Dynamisches Konfigurationsformular bietet Vergleichswerte mit Profil an
 
**Version 3.50:**  
- Release für IPS 6.3  
- Dynamisches Konfigurationsformular welches Vergleichswerte auf Basis der Quelle anbietet  

**Version 3.10:**  
- Release für IPS 6.1  
- Dynamisches Konfigurationsformular welche die nicht benötigten Vergleichswerte ausblendet  
   
**Version 3.00:**  
- Release für IPS 5.1 und den Module-Store  
- IPS_SetProperty und IPS_Applychanges auf sich selbst entfernt   

**Version 2.02:**  
- Fixes für IPS 5.0

**Version 2.01:**
- Release für IPS 4.3  

**Version 2.0:**  
- Release für IPS 4.1  

**Version 1.0:**  
- Release für IPS 4.0  

### 4. Spenden

  Die Library ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

  PayPal:  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](https://paypal.me/Nall4chan)  

  Wunschliste:  
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share)  

## 6. Lizenz  

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
