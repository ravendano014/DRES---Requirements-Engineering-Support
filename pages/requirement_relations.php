<?php
#############################################################################
## requirement_relations.php - requirement relations tab                   ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$RelationsForm = new XMLForm("forms/RequirementRelations.xml", $PARAMS);
$RelationForm = new XMLForm("forms/RequirementRelation.xml", $PARAMS);

if ($req_id == $CHECKOUT)
	$requirement = @load_file($CHECKOUT_FILE);
elseif($req_id)
	$requirement = $DataSrc->get_requirement($req_id);
show_error("not supported yet");
echo $RelationsForm->render($requirement);
echo $RelationForm->render($requirement);
?>