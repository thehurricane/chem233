<?php
/*
This file contains the class definitions of the objects used in this system.
This is definitely the ugliest part of the system. The Molecule contructor method and equals function are not very fun to look at, but they are also at the core of the app.
*/
//Class that holds Molecule data
class Molecule {
	//Contains an array representation of all the Atoms in the Molecule
	public $atomArray;
	//Contains an array representation of all the Bonds in the Molecule
	public $bondArray;
	//Contains an array representation of all the ElectronFlows in the Molecule
	public $electronFlowArray;
	
	/*
	Create a molecule object from a .mrv file, specified by
	@param filepath - the file path of the .mrv file
	@return - the Molecule object
	*/
	//TODO: Remove spaces from electron flow identifiers
	function __construct($filepath) {
		//Load the xml file as a php object
		$xmlFile = simplexml_load_file($filepath);
		if ($xmlFile == false) {
			//echo "<p>DEBUG: Couldn't load file from $filepath</p>\n";
			return false;
		} else {
			//echo "<p>DEBUG: Loaded file okay from $filepath</p>\n";
		}
		
		$this->atomArray = array();
		//Get the atom array from the document
		$atomArrayXML = $xmlFile->MDocument->MChemicalStruct->molecule->atomArray;
		if (empty($atomArrayXML)) {
			$atomArrayXML = $xmlFile->MDocument->MChemicalStruct->reaction->reactantList->molecule->atomArray;
		}
	
		//Get the atom array ids and put them into an array.
		$atomID = $atomArrayXML['atomID'];
		$atomIDs = str_word_count($atomID, 1, '0123456789');
		$numAtoms = count($atomIDs);
		//echo "<p>DEBUG: $numAtoms atoms in file</p>\n";
	
		//Get the element types and put them into an array.
		$elementType = $atomArrayXML['elementType'];
		$elements = str_word_count($elementType,1);
	
		//TODO: Test this to see if it works even when only some of the atoms have a charge
		//Get the formal charge for each atom and place it in an array
		$formalCharge = $atomArrayXML['formalCharge'];
		if( $formalCharge == NULL) {
			for($i = 0; $i < count($atomIDs); $i++) {
				$charges[$i] = 0;	
			}
		} else {
			$charges = str_word_count($formalCharge, 1, '0123456789');
		}
		
		//TODO: Test this to see if it works when only some of the atoms have lone pairs
		//Get the number of lone pairs for each atom and place it in an array
		$lonePair = $atomArrayXML['lonePair'];
		if( $lonePair == NULL) {
			for($i = 0; $i < count($atomIDs); $i++) {
				$lonePairs[$i] = 0;	
			}
		} else {
			$lonePairs = str_word_count($lonePair, 1, '0123456789');
		}
		
		//Create an array containing each atom and all its information
		for($i = 0; $i < $numAtoms; $i++) {
			$this->atomArray[$i] = new Atom($atomIDs[$i], $elements[$i], $charges[$i], $lonePairs[$i]);
			//$this->atomArray[$i]->printAtom();
		}
		
		//Create an array containing each bond and all its information
		$bondArrayXML = $xmlFile->MDocument->MChemicalStruct->molecule->bondArray;
		if (empty($bondArrayXML)) {
			$bondArrayXML = $xmlFile->MDocument->MChemicalStruct->reaction->reactantList->molecule->bondArray;
		}
		$numBonds = count($bondArrayXML);
		//echo "<p>DEBUG: $numBonds bonds in file</p>\n";
	
		for($i = 0; $i < $numBonds; $i++) {
			$bond = $bondArrayXML->bond[$i];
			$bondedAtoms = $bond['atomRefs2'];
			$bondInfo = str_word_count($bondedAtoms,1, '0123456789');
			$bondInfo[2] = (int)$bond['order'];
			$this->bondArray[$i] = new Bond($bondInfo[0], $bondInfo[1], $bondInfo[2]);
			//$this->bondArray[$i]->printBond();
		}
		
		//Create an array containing each electron flow and all its information
		$electronFlowArrayXML = $xmlFile->MDocument->MEFlow;
		$numelectronFlows = count($electronFlowArrayXML);
		//echo "<p>DEBUG: $numelectronFlows electron flows in file</p>\n";
		for($i = 0; $i < $numelectronFlows; $i++) {
			$startPoint = $xmlFile->MDocument->MEFlow[$i]->MEFlowBasePoint['atomRef'];
			$startAtom1 = NULL;
			$startAtom2 = NULL;
			$endAtom1 = NULL;
			$endAtom2 = NULL;
			//Check whether flow originates from an atom or a bond.
			if ($startPoint == NULL) {
				$startPoint = $xmlFile->MDocument->MEFlow[$i]->MAtomSetPoint[0]['atomRefs'];
				$startAtoms = preg_split('/m1./', $startPoint, 0, PREG_SPLIT_NO_EMPTY);
				$startAtom1 = trim($startAtoms[0]);
				$startAtom2 = trim($startAtoms[1]);
				$endPoint = $xmlFile->MDocument->MEFlow[$i]->MAtomSetPoint[1]['atomRefs'];
				$endAtoms = preg_split('/m1./', $endPoint, 0, PREG_SPLIT_NO_EMPTY);
				if (count($endAtoms) > 1) {
					$endAtom1 = trim($endAtoms[0]);
					$endAtom2 = trim($endAtoms[1]);
				} else {
					$endAtom1 = trim($endAtoms[0]);
				}
			} else {
				$startAtoms = preg_split('/m1./', $startPoint, 0, PREG_SPLIT_NO_EMPTY);
				$startAtom1 = trim($startAtoms[0]);
				
				$endPoint = $xmlFile->MDocument->MEFlow[$i]->MAtomSetPoint[0]['atomRefs'];
				$endAtoms = preg_split('/m1./', $endPoint, 0, PREG_SPLIT_NO_EMPTY);
				if (count($endAtoms) > 1) {
					$endAtom1 = trim($endAtoms[0]);
					$endAtom2 = trim($endAtoms[1]);
				} else {
					$endAtom1 = trim($endAtoms[0]);
				}
			}
			if ($xmlFile->MDocument->MEFlow[$i]['headFlags'] == NULL) {
				$numberOfElectrons = 2;
			} else {
				$numberOfElectrons = 1;
			}
			$this->electronFlowArray[$i] = new ElectronFlow($startAtom1, $startAtom2, $endAtom1, $endAtom2, $numberOfElectrons);
			//$electronFlowArray[$i]->printElectronFlow();
		}
	}
	
	/*
	Prints out the molecule's attributes on the page
	*/
	public function printMolecule() {
		echo "<p>PRINT MOLECULE</p>\n";
		foreach ($this->atomArray as $currentAtom) {
			$currentAtom->printAtom();
		}
		foreach ($this->bondArray as $currentBond) {
			$currentBond->printBond();
		}
		foreach ($this->electronFlowArray as $currentElectronFlow) {
			$currentElectronFlow->printElectronFlow();
		}
	}
	
	/*
	Compares one molecule against another.
	@param other - the other molecule object to compare against
	@return - string: "equal" if the two molecules are equal; an error message otherwise
	*/
	public function equals($other) {
		//Check to see if each intermediate has the same number of atoms.
		if (count($this->atomArray) != count($other->atomArray)) {
			return "Error: Incorrect number of atoms";
		}
		//Check to see if each intermediate has the same number of bonds.
		if (count($this->bondArray) != count($other->bondArray)) {
			return "Error: Incorrect number of bonds";
		}		
		//Check to see if each intermediate has the same number of electron flows.
		if (count($this->electronFlowArray) != count($other->electronFlowArray)) {
			return "Error: Incorrect number of electron flows";
		}
		//Check all the atoms in each file against one another.
		/*
		Here is an example of the result of this next part.
		Suppose $this contains an atom array like this: atomID="a1 a2 a3 a4 a5" elementType="C C N O H"
		Suppose $other contains an atom array like this: atomID="a1 a2 a3 a4 a5" elementType="C C N O H"
		This code will result in a 2 dimensional array that will look something like this:
		
		atomKeyValueArray[a1]-> {a1, a2}
		atomKeyValueArray[a2]-> {a1, a2}
		atomKeyValueArray[a3]-> {a3}
		atomKeyValueArray[a4]-> {a4}
		atomKeyValueArray[a4]-> {a5}
		
		Note that $this.a1 could equal $other.a1 or $other.a2
		*/
		//Structure to hold the atoms and their potential equivalents in the other file
		$atomKeyValueArray = array();
		foreach ($this->atomArray as $thisCurrentAtom) {
			$atomKeyValueArray[$thisCurrentAtom->atomID] = array();
			foreach ($other->atomArray as $otherCurrentAtom) {
				//echo "<p>For each atom in the other atom array...</p>\n";
				if ($thisCurrentAtom->equals($otherCurrentAtom)) {
					//echo "<p>Atoms are equal</p>\n";
					$atomKeyValueArray[$thisCurrentAtom->atomID][$otherCurrentAtom->atomID] = $otherCurrentAtom->atomID;
				} else {
					//echo "<p>Atoms are not equal</p>\n";
				}
			}
			if (count($atomKeyValueArray[$thisCurrentAtom->atomID]) == 0) {
				return "Error: Some atom's properties are incorrect";
			}
			//print_r($atomKeyValueArray[$thisCurrentAtom->atomID]);
		}

		//Check all the bonds in each file against one another.
		/*
		Here is an example of the result of this next part.
		Suppose $this contains 3 bonds as follows: <bond atomRefs2="a1 a2" order="1"/><bond atomRefs2="a2 a3" order="1"/><bond atomRefs2="a2 a4" order="2">
		This code will result in an array that looks like this:
		
		$thisBondKeyValueArray[a1] = 1
		$thisBondKeyValueArray[a2] = 3
		$thisBondKeyValueArray[a3] = 1
		$thisBondKeyValueArray[a4] = 1
		
		The numbers on the right correspond to the number of bonds each atom is a part of
		*/
		//Structure to hold the atoms and the number of bonds they have (in $this file)
		$thisBondKeyValueArray = array();
		foreach ($this->bondArray as $thisCurrentBond) {
			//echo "<p>$thisCurrentBond->atomRef1</p>\n";
			//echo "<p>$thisCurrentBond->atomRef2</p>\n";
			if (!isset($thisBondKeyValueArray[$thisCurrentBond->atomRef1])) {
				$thisBondKeyValueArray[$thisCurrentBond->atomRef1] = 1;
			} else {
				$thisBondKeyValueArray[$thisCurrentBond->atomRef1]++;
			}
			if (!isset($thisBondKeyValueArray[$thisCurrentBond->atomRef2])) {
				$thisBondKeyValueArray[$thisCurrentBond->atomRef2] = 1;
			} else {
				$thisBondKeyValueArray[$thisCurrentBond->atomRef2]++;
			}
		}
		//print_r($thisBondKeyValueArray);
		//Structure to hold the atoms and the number of bonds they have (in $other file)
		$otherBondKeyValueArray = array();
		//This does the same as the code above, only with the $otherBondKeyValueArray structure instead
		foreach ($other->bondArray as $otherCurrentBond) {
			//echo "<p>$thisCurrentBond->atomRef1</p>\n";
			//echo "<p>$thisCurrentBond->atomRef2</p>\n";
			print_r($otherBondKeyValueArray);
			if (!isset($otherBondKeyValueArray[$otherCurrentBond->atomRef1])) {
				$otherBondKeyValueArray[$otherCurrentBond->atomRef1] = 1;
			} else {
				$otherBondKeyValueArray[$otherCurrentBond->atomRef1]++;
			}
			if (!isset($otherBondKeyValueArray[$otherCurrentBond->atomRef2])) {
				$otherBondKeyValueArray[$otherCurrentBond->atomRef2] = 1;
			} else {
				$otherBondKeyValueArray[$otherCurrentBond->atomRef2]++;
			}
		}
		//print_r($otherBondKeyValueArray);
		
		//Now check the 2 data structures against each other (using the atom array as well to check different atoms of the same element). If there are any non-matches, remove them from the array structure.
		foreach ($atomKeyValueArray as $currentAtomID => $currentAtom) {
			//echo "<p><b>$currentAtomID</b></p>\n";
			foreach ($currentAtom as $matchingAtomID) {
				//echo "<p>$matchingAtomID</p>\n";
				if ((isset($thisBondKeyValueArray[$currentAtomID])) && (isset($otherBondKeyValueArray[$matchingAtomID])) && ($thisBondKeyValueArray[$currentAtomID] != $thisBondKeyValueArray[$currentAtomID])) {
					//These atoms do NOT match. Remove from the array
					//echo "<p>$currentAtomID DOESN'T match $matchingAtomID</p>\n";
					unset($atomKeyValueArray[$currentAtomID][$matchingAtomID]);
				} else {
					//These atoms match
					//echo "<p>$currentAtomID matches $matchingAtomID</p>\n";
				}
			}
			if ($atomKeyValueArray[$currentAtomID] == null) {
				//There are no matches for this atom
				return "Error: One or more atoms have different properties than the solution";
				//echo "<p>No matches on this atom.</p>\n";
				//return false;
			} else if (count($atomKeyValueArray[$currentAtomID]) == 0) {
				//There are no matches for this atom
				return "Error: One or more atoms have different properties than the solution";
				//echo "<p>No matches on this atom.</p>\n";
				//return false;
			}
			else if (count($atomKeyValueArray[$currentAtomID]) > 1) {
				//There are multiple matches for this atom. This could be tricky...
				//echo "<p>Multiple matches on this atom! Yikes!</p>\n";
			}
			//print_r($atomKeyValueArray[$currentAtomID]);
		}
		//TODO: Check to see if the bonds are between the right elements
		//ACTUALLY, I might not have to do this. Testing will reveal whether this is needed or not.
		//It might be able to be proven mathematically (or by proof by contradiction) or something that it is an impossible situation

		//Check which atoms/bonds are involved in electron flows
		
		/*
		Here is an example of the result of this next part.
		Suppose $this contains two electron flows at 0 and 1
		Suppose $other contains three electron flows at 0, 1, and 2
		This code will result in a 2 dimensional array that will look something like this:
		
		electronFlowKeyValueArray[0]-> {0, 1}
		electronFlowKeyValueArray[1]-> {2}
		
		*/
		
		//Structure to hold the electron flows and their potential equivalents in the other file
		$electronFlowKeyValueArray = array();
		//For each electron flow in this molecule,
		for ($i = 0; $i < count($this->electronFlowArray); $i++) {
			$electronFlowKeyValueArray[$i] = array();
			//For each electron flow in the other molecule,
			for ($j = 0; $j < count($other->electronFlowArray); $j++) {
				//Check whether the two electron flows match, and if they do,
				if ($this->electronFlowArray[$i]->equals($other->electronFlowArray[$j])) {
					//Add the electron flow to the map of matched electron flows
					$electronFlowKeyValueArray[$i][$j] = $j;
				}
			}
			//Check if the current electron flow maps to anything. If it doesn't return an error.
			if (count($electronFlowKeyValueArray[$i]) == 0) {
				return "Error: One or more electron flows has a problem";
			}
		}
		//print_r($electronFlowKeyValueArray);
		foreach($this->electronFlowArray as $thisCurrentElectronFlow) {
			$matchStart1 = false;
			$matchStart2 = false;
			$matchEnd1 = false;
			$matchEnd2 = false;
			foreach($other->electronFlowArray as $otherCurrentElectronFlow) {
				if (($thisCurrentElectronFlow->startAtom2 == null) && ($otherCurrentElectronFlow->startAtom2 == null)) {
					//Starts at an atom
					//echo "<p>Starts at an atom</p>\n";
					foreach($atomKeyValueArray[$thisCurrentElectronFlow->startAtom1] as $currentValue) {
						if (strcmp($currentValue, $otherCurrentElectronFlow->startAtom1) == 0) {
							$matchStart1 = true;
							$matchStart2 = true;
						}
					}
				} else if ($thisCurrentElectronFlow->startAtom2 == null) {
					//otherCurrentElectronFlow originates at a bond, thisCurrentElectronFlow does not
				} else if ($otherCurrentElectronFlow->startAtom2 == null) {
					//thisCurrentElectronFlow originates at a bond, otherCurrentElectronFlow does not	
				} else {
					//Starts at a bond
					//echo "<p>Starts at a bond</p>\n";
					foreach($atomKeyValueArray[$thisCurrentElectronFlow->startAtom1] as $currentValue) {
						//echo "Atom 1 ";
						if (strcmp($currentValue, $otherCurrentElectronFlow->startAtom1) == 0) {
							$matchStart1 = true;
						}
					}
					foreach($atomKeyValueArray[$thisCurrentElectronFlow->startAtom2] as $currentValue) {
						//echo "Atom 2 ";
						if (strcmp($currentValue, $otherCurrentElectronFlow->startAtom2) == 0) {
							$matchStart2 = true;
						}
					}
				}
			}
			foreach($other->electronFlowArray as $otherCurrentElectronFlow) {
				if (($thisCurrentElectronFlow->endAtom2 == null) && ($otherCurrentElectronFlow->endAtom2 == null)) {
					//Ends at one atom
					foreach($atomKeyValueArray[$thisCurrentElectronFlow->endAtom1] as $currentValue) {
						if (strcmp($currentValue, $otherCurrentElectronFlow->endAtom1) == 0) {
							$matchEnd1 = true;
							$matchEnd2 = true;
						}
					}
				} else {
					//Ends at a bond
					foreach($atomKeyValueArray[$thisCurrentElectronFlow->endAtom1] as $currentValue) {
						if (strcmp($currentValue, $otherCurrentElectronFlow->endAtom1) == 0) {
							$matchEnd1 = true;
						}
					}
					foreach($atomKeyValueArray[$thisCurrentElectronFlow->endAtom2] as $currentValue) {
						if (strcmp($currentValue, $otherCurrentElectronFlow->endAtom2) == 0) {
							$matchEnd2 = true;
						}
					}
				}
			}
			if ($matchStart1 == false) {
				return "Error: An electron flow originates at an incorrect location";
				//echo "<p>An electron flow starts(1) at different spots</p>\n";
				//return false;
			} else if ($matchStart2 == false) {
				return "Error: An electron flow originates at an incorrect location";
				//echo "<p>An electron flow starts(2) at different spots</p>\n";
				//return false;
			} else if ($matchEnd1 == false) {
				return "Error: An electron flow ends at an incorrect location";
				//echo "<p>An electron flow ends(1) at different spots</p>\n";
				//return false;
			} else if ($matchEnd2 == false) {
				return "Error: An electron flow ends at an incorrect location";
				//echo "<p>An electron flow ends(2) at different spots</p>\n";
				//return false;
			} else {
				//echo "<p>Electron flow match</p>\n";
			}
		}
		//echo "<p>Done</p>\n";
		//return true;
		return "equal";
	}
}

//Class that holds Atom data
class Atom {
	public $atomID;
	public $element;
	public $charge;
	public $lonePair;
	
	function __construct($atomID, $element, $charge, $lonePair) {
		$this->atomID = $atomID;
		$this->element = $element;
		$this->charge = $charge;
		$this->lonePair = $lonePair;
	}
	
	public function printAtom() {
		echo "<p>PRINT ATOM: ID: $this->atomID, Element: $this->element, Charge: $this->charge, Number of Lone Pairs: $this->lonePair</p>\n";
	}
	
	public function equals($other) {
		//echo "<p>Check atom equality</p>\n";
		if (strcmp($this->element, $other->element) == 0) {
			if (strcmp($this->charge, $other->charge) == 0) {
				if (strcmp($this->lonePair, $other->lonePair) == 0) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}

//Class that holds Bond data
class Bond {
	public $atomRef1;
	public $atomRef2;
	public $order;
	
	function __construct($atomRef1, $atomRef2, $order) {
		$this->atomRef1 = $atomRef1;
		$this->atomRef2 = $atomRef2;
		$this->order = $order;
	}
	
	public function printBond() {
		echo "<p>PRINT BOND: AtomRef1: $this->atomRef1, AtomRef2: $this->atomRef2, Order: $this->order</p>\n";
	}
	
	public function equals($other) {
		//echo "<p>Check bond equality</p>\n";
		if (strcmp($this->order, $other->order) == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

//Class that holds ElectronFlow data
class ElectronFlow {
	public $startAtom1;
	public $startAtom2;
	public $endAtom1;
	public $endAtom2;
	public $numberOfElectrons;
	
	function __construct($startAtom1, $startAtom2, $endAtom1, $endAtom2, $numberOfElectrons) {
		$this->startAtom1 = $startAtom1;
		$this->startAtom2 = $startAtom2;
		$this->endAtom1 = $endAtom1;
		$this->endAtom2 = $endAtom2;
		$this->numberOfElectrons = $numberOfElectrons;
	}
	
	public function printElectronFlow() {
		echo "<p>PRINT ELECTRON FLOW: ";
		if (($this->startAtom2 == NULL) && ($this->endAtom2 == NULL)) {
			echo "Start atom: $this->startAtom1, End atom: $this->endAtom1, Number of electrons: $this->numberOfElectrons</p>\n";
		} else if ($this->endAtom2 == NULL) {
			echo "Start bond: $this->startAtom1 $this->startAtom2, End atom: $this->endAtom1, Number of electrons: $this->numberOfElectrons</p>\n";
		} else if ($this->startAtom2 == NULL) {
			echo "Start atom: $this->startAtom1, End bond: $this->endAtom1 $this->endAtom2, Number of electrons: $this->numberOfElectrons</p>\n";
		} else{
			echo "Start bond: $this->startAtom1 $this->startAtom2, End bond: $this->endAtom1 $this->endAtom2, Number of electrons: $this->numberOfElectrons</p>\n";
		}
	}
	
	public function equals($other) {
		//echo "<p>Check electron flow equality</p>\n";
		if (($this->startAtom2 == NULL) && ($other->startAtom2 == NULL)) {
			if (($this->endAtom2 == NULL) && ($other->endAtom2 == NULL)) {
				if ($this->numberOfElectrons == $other->numberOfElectrons) {
					return TRUE;
				} else {
					return FALSE;
				}
			} else if (($this->endAtom2 != NULL) && ($other->endAtom2 != NULL)) {
				if ($this->numberOfElectrons == $other->numberOfElectrons) {
					return TRUE;
				} else {
					return FALSE;
				}
			}
		} else if (($this->startAtom2 != NULL) && ($other->startAtom2 != NULL)) {
			if (($this->endAtom2 == NULL) && ($other->endAtom2 == NULL)) {
				if ($this->numberOfElectrons == $other->numberOfElectrons) {
					return TRUE;
				} else {
					return FALSE;
				}
			} else if (($this->endAtom2 != NULL) && ($other->endAtom2 != NULL)) {
				if ($this->numberOfElectrons == $other->numberOfElectrons) {
					return TRUE;
				} else {
					return FALSE;
				}
			}
		} else {
			return FALSE;
		}
	}
}
?>