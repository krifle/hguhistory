<?php if(!defined('__ZBXE__')) exit();
$query = new Query();
$query->setQueryId("getGrantedModule");
$query->setAction("select");
$query->setPriority("");

${'module_srl55_argument'} = new ConditionArgument('module_srl', $args->module_srl, 'equal');
${'module_srl55_argument'}->checkFilter('number');
${'module_srl55_argument'}->checkNotNull();
${'module_srl55_argument'}->createConditionValue();
if(!${'module_srl55_argument'}->isValid()) return ${'module_srl55_argument'}->getErrorMessage();
if(${'module_srl55_argument'} !== null) ${'module_srl55_argument'}->setColumnType('number');

$query->setColumns(array(
new SelectExpression('count(*)', '`count`')
));
$query->setTables(array(
new Table('`xe_module_grants`', '`module_grants`')
));
$query->setConditions(array(
new ConditionGroup(array(
new ConditionWithArgument('`module_srl`',$module_srl55_argument,"equal")
,new ConditionWithoutArgument('`name`',"'access','view','list'","in", 'and')))
,new ConditionGroup(array(
new ConditionWithoutArgument('`group_srl`','1',"more")
,new ConditionWithoutArgument('`group_srl`','-1',"equal", 'or')
,new ConditionWithoutArgument('`group_srl`','-2',"equal", 'or')),'and')
));
$query->setGroups(array());
$query->setOrder(array());
$query->setLimit();
return $query; ?>